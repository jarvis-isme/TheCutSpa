<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Role;
use App\Models\Shift;
use App\Models\User;
use App\Models\UserShift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    /** Prefix */
    const PREFIX = 'manager';

    /** Api url */
    const API_URL_CREATE_STAFF = 'staff/create';
    const API_URL_UPDATE_STAFF = 'staff/update/{staffId}';
    const API_URL_DELETE_STAFF = 'staff/delete/{staffId}';
    const API_URL_GET_ALL_STAFFS = 'staff/get-all';

    const API_URL_CREATE_SHIFT = 'shift/create';
    const API_URL_UPDATE_SHIFT = 'shift/update/{shiftId}';
    const API_URL_DELETE_SHIFT = 'shift/delete/{shiftId}';
    const API_URL_GET_ALL_SHIFTS = 'shift/get-all';

    /** Method */
    const METHOD_CREATE_STAFF = 'createStaff';
    const METHOD_UPDATE_STAFF = 'updateStaff';
    const METHOD_DELETE_STAFF = 'deleteStaff';
    const METHOD_GET_ALL_STAFFS = 'getAllStaffs';

    const METHOD_CREATE_SHIFT = 'createShift';
    const METHOD_UPDATE_SHIFT = 'updateShift';
    const METHOD_DELETE_SHIFT = 'deleteShift';
    const METHOD_GET_ALL_SHIFTS = 'getAllShifts';

    /**
     * @functionName: createStaff
     * @type:         public
     * @param:        Rquest
     * @return:       String(Json)
     */
    public function createStaff(Request $request)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $name = $request->{User::COL_NAME};
            $phone = $request->{User::COL_PHONE};
            $email = $request->{User::COL_EMAIL};
            $password = $request->{User::COL_PASSWORD};
            $gender = $request->{User::COL_GENDER};
            $birthDay = $request->{User::COL_BIRTHDAY};

            $validator = User::validator([
                User::COL_NAME => $name,
                User::COL_PHONE => $phone,
                User::COL_EMAIL => $email,
                User::COL_PASSWORD => $password,
                User::COL_GENDER => $gender,
                User::COL_BIRTHDAY => $birthDay,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }
            if (!$phone) {
                return self::responseERR('ERR400xxx', 'Staffs must have phone number.');
            }
            $request->validate([File::VAL_FILE => File::FILE_VALIDATIONS[File::IMAGE_TYPE]]);

            $storeId = Auth::user()->{User::COL_STORE_ID};
            $dataCreate = [
                User::COL_NAME => $name,
                User::COL_PHONE => $phone,
                User::COL_EMAIL => $email,
                User::COL_PASSWORD => bcrypt($password),
                User::COL_GENDER => $gender,
                User::COL_BIRTHDAY => $birthDay,
                User::COL_ROLE_ID => User::STAFF_ROLE_ID,
                User::COL_STORE_ID => $storeId,
            ];
            DB::beginTransaction();
            $dataImages = [];
            $maxImages = (int) getenv('MAX_USER_IMAGE');
            if ($maxImages == 0) {
                $maxImages = 1;
            }
            $staff = User::create($dataCreate);
            if (!$staff) {
                return self::responseERR('ERR400xxx', 'Create staff failed1.');
            }
            for ($i = 0; $i < $maxImages; $i++) {
                $dataImage = [
                    File::COL_OWNER_ID => $staff->{User::COL_ID},
                    File::COL_OWNER_TYPE => User::class,
                    File::COL_PATH => getenv('DEFAULT_USER_AVATAR_URL'),
                    File::COL_TYPE => File::IMAGE_TYPE,
                    File::COL_CREATED_AT => now()
                ];
                array_push($dataImages, $dataImage);
            }
            if (!File::insert($dataImage)) {
                DB::rollBack();
                return self::responseERR('ERR400xxx', 'Create staff failed2.');
            }
            if ($request->has('file')) {
                $fileId = $staff->files->first()->{File::COL_ID};
                $request->fileId = $fileId;
                $request->type = File::IMAGE_TYPE;
                $fileController = new FileController();
                $responseSaveFile = $fileController->uploadFileS3($request)->getData();
                if ($responseSaveFile->code != 200) {
                    DB::rollBack();
                    return self::responseERR('ERR400xxx', 'Create staff failed3.');
                }
            }
            DB::commit();
            $staff = User::find($staff->{User::COL_ID});
            return self::responseST('ST200xxx', 'Create staff successfully.', ['newStaff' => $staff]);
        } catch (Exception $ex) {
            DB::rollBack();
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: getAllStaffs
     * @type:         public
     * @param:        Rquest
     * @return:       String(Json)
     */
    public function getAllStaffs(Request $request)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $storeId = Auth::user()->{User::COL_STORE_ID};

            $staffs = User::where(User::COL_STORE_ID, $storeId)
                ->where(User::COL_ROLE_ID, User::STAFF_ROLE_ID)->get();

            $data = [
                'staffs' => $staffs,
            ];

            return self::responseST('ST200xxx', 'Get all staffs successfully.', $data);
        } catch (Exception $ex) {
            DB::rollBack();
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: updateStaff
     * @type:         public
     * @param:        Request $request, $staffId
     * @return:       String(Json)
     */
    public function updateStaff(Request $request, $staffId)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $name = $request->{User::COL_NAME};
            $phone = $request->{User::COL_PHONE};
            $email = $request->{User::COL_EMAIL};
            $password = $request->{User::COL_PASSWORD};
            $gender = $request->{User::COL_GENDER};
            $birthDay = $request->{User::COL_BIRTHDAY};

            $validator = User::validator([
                User::COL_NAME => $name,
                User::COL_PHONE => $phone,
                User::COL_EMAIL => $email,
                User::COL_PASSWORD => $password,
                User::COL_GENDER => $gender,
                User::COL_BIRTHDAY => $birthDay,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }
            if (!$phone) {
                return self::responseERR('ERR400xxx', 'Staffs must have phone number.');
            }
            $request->validate([File::VAL_FILE => File::FILE_VALIDATIONS[File::IMAGE_TYPE]]);

            $staff = User::where(User::COL_ROLE_ID, User::STAFF_ROLE_ID)
                ->where(User::COL_ID, $staffId)->first();
            if (!$staff) {
                return self::responseERR('ERR400xxx', 'Not found staff');
            }
            DB::beginTransaction();
            $staff->{User::COL_NAME} = $name;
            $staff->{User::COL_PHONE} = $phone;
            $staff->{User::COL_EMAIL} = $email;
            $staff->{User::COL_PASSWORD} = bcrypt($password);
            $staff->{User::COL_GENDER} = $gender;
            $staff->{User::COL_BIRTHDAY} = $birthDay;
            $rsSave = $staff->save();
            if (!$rsSave) {
                DB::rollBack();
                return self::responseERR('ERR400xxx', 'Update staff failed1.');
            }

            if ($request->has('file')) {
                $fileId = $staff->files->first()->{File::COL_ID};
                $request->fileId = $fileId;
                $request->type = File::IMAGE_TYPE;
                $fileController = new FileController();
                $responseSaveFile = $fileController->uploadFileS3($request)->getData();
                if ($responseSaveFile->code != 200) {
                    DB::rollBack();
                    return self::responseERR('ERR400xxx', 'Update staff failed2.');
                }
            } elseif ($request->has('shiftIds')) {
                $storeId = Auth::user()->{User::COL_STORE_ID};
                $shiftIds = array_unique($request->shiftIds);
                $shiftFound = Shift::whereIn(Shift::COL_ID, $shiftIds)
                    ->where(Shift::COL_STORE_ID, $storeId)->pluck(Shift::COL_ID);
                if (count($shiftIds) != count($shiftFound)) {
                    DB::rollBack();
                    return self::responseERR('ERR400xxx', 'Update staff failed - there are invalid shifts.');
                }

                UserShift::where(UserShift::COL_USER_ID, $staffId)
                    ->whereIn(UserShift::COL_SHIFT_ID, $shiftIds)->delete();

                $dataInsert = [];
                foreach ($shiftIds as $shiftId) {
                    $data = [
                        UserShift::COL_USER_ID => $staffId,
                        UserShift::COL_SHIFT_ID => $shiftId,
                    ];
                    array_push($dataInsert, $data);
                }
                $rsInsert = UserShift::insert($dataInsert);
                if (!$rsInsert) {
                    DB::rollBack();
                    return self::responseERR('ERR400xxx', 'Update staff failed.');
                }
            }
            DB::commit();
            $staff = User::find($staffId);
            return self::responseST('ST200xxx', 'Update staff successfully.', ['newStaff' => $staff]);
        } catch (Exception $ex) {
            DB::rollBack();
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: deleteStaff
     * @type:         public
     * @param:        int $staffId
     * @return:       String(Json)
     */
    public function deleteStaff(int $staffId)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $validator = User::validator([
                User::COL_ID => $staffId,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }
            $staff = User::where(User::COL_ID, $staffId)
                ->where(User::COL_ROLE_ID, User::STAFF_ROLE_ID)
                ->first();
            if (!$staff) {
                return self::responseERR('ERR400xxx', 'Not found staff.');
            }
            DB::beginTransaction();
            if (!$staff->files()->delete() or !$staff->delete()) {
                DB::rollBack();
                return self::responseERR('ERR400xxx', 'Delete staff failed.');
            }
            DB::commit();
            return self::responseST('ST200xxx', 'Delete staff successfully.');
        } catch (Exception $ex) {
            DB::rollBack();
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: createShift
     * @type:         public
     * @param:        Rquest
     * @return:       String(Json)
     */
    public function createShift(Request $request)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $startTime = $request->{Shift::VAL_START_TIME};
            $endTime = $request->{Shift::VAL_END_TIME};
            $dayInWeek = $request->{Shift::VAL_DAY_IN_WEEK};
            $shiftName = $request->{Shift::VAL_SHIFT_NAME};

            $validator = Shift::validator([
                Shift::VAL_START_TIME => $startTime,
                Shift::VAL_END_TIME => $endTime,
                Shift::VAL_DAY_IN_WEEK => $dayInWeek,
                Shift::VAL_SHIFT_NAME => $shiftName,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }

            $storeId = Auth::user()->{User::COL_STORE_ID};
            $dataCreate = [
                Shift::COL_START_TIME => $startTime,
                Shift::COL_END_TIME => $endTime,
                Shift::COL_DAY_IN_WEEK => $dayInWeek,
                Shift::COL_SHIFT_NAME => $shiftName,
                Shift::COL_STORE_ID => $storeId,
            ];

            $shift = Shift::create($dataCreate);
            if (!$shift) {
                return self::responseERR('ERR400xxx', 'Create shift failed.');
            }
            return self::responseST('ST200xxx', 'Create shift successfully.', ['newShift' => $shift]);
        } catch (Exception $ex) {
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: getAllShifts
     * @type:         public
     * @param:        Empty
     * @return:       String(Json)
     */
    public function getAllShifts()
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $storeId = Auth::user()->{User::COL_STORE_ID};

            $shifts = Shift::where(Shift::COL_STORE_ID, $storeId)->get();

            $data = [
                'shifts' => $shifts,
            ];

            return self::responseST('ST200xxx', 'Get all shifts successfully.', $data);
        } catch (Exception $ex) {
            DB::rollBack();
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: updateShift
     * @type:         public
     * @param:        Request $request, $shiftId
     * @return:       String(Json)
     */
    public function updateShift(Request $request, $shiftId)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $startTime = $request->{Shift::VAL_START_TIME};
            $endTime = $request->{Shift::VAL_END_TIME};
            $dayInWeek = $request->{Shift::VAL_DAY_IN_WEEK};
            $shiftName = $request->{Shift::VAL_SHIFT_NAME};

            $validator = Shift::validator([
                Shift::VAL_START_TIME => $startTime,
                Shift::VAL_END_TIME => $endTime,
                Shift::VAL_DAY_IN_WEEK => $dayInWeek,
                Shift::VAL_SHIFT_NAME => $shiftName,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }

            $shift = Shift::find($shiftId);
            if (!$shift) {
                return self::responseERR('ERR400xxx', 'Not found shift');
            }
            $shift->{Shift::COL_START_TIME} = $startTime;
            $shift->{Shift::COL_END_TIME} = $endTime;
            $shift->{Shift::COL_DAY_IN_WEEK} = $dayInWeek;
            $shift->{Shift::COL_SHIFT_NAME} = $shiftName;
            $rsSave = $shift->save();
            if (!$rsSave) {
                return self::responseERR('ERR400xxx', 'Update shift failed.');
            }

            return self::responseST('ST200xxx', 'Update shift successfully.', ['newShift' => $shift]);
        } catch (Exception $ex) {
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }

    /**
     * @functionName: deleteShift
     * @type:         public
     * @param:        int $shiftId
     * @return:       String(Json)
     */
    public function deleteShift(int $shiftId)
    {
        if (!$this->isManager()) {
            return self::responseERR(self::YOUR_ROLE_CANNOT_CALL_THIS_API, self::M_YOUR_ROLE_CANNOT_CALL_THIS_API);
        }
        try {
            $validator = Shift::validator([
                Shift::COL_ID => $shiftId,
            ]);
            if ($validator->fails()) {
                return self::responseIER($validator->errors()->first());
            }
            $shift = Shift::find($shiftId);
            if (!$shift) {
                return self::responseERR('ERR400xxx', 'Not found shift.');
            }
            if (!$shift->delete()) {
                return self::responseERR('ERR400xxx', 'Delete shift failed.');
            }
            return self::responseST('ST200xxx', 'Delete shift successfully.');
        } catch (Exception $ex) {
            return self::responseEX('EX500xxx', $ex->getMessage());
        }
    }
}