<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiswaModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Siswa extends Controller
{
    public function __construct()
    {
        $this->student = new SiswaModel();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $data = null;

        if (isset($query['sortby'])) {
            $data = SiswaModel::orderBy('kelas', $query['sortby']);
        } else {
            $data = SiswaModel::orderBy('kelas');
        }

        $count = $data->get()->count();

        if (isset($query['kelas'])) {
            $data = $data->where('kelas', 'LIKE', "%{$query['kelas']}%");
        }
        if (isset($query['nisn'])) {
            $data = $data->where('nisn', 'LIKE', "%{$query['nisn']}%");
        }

        if (isset($query['nipd'])) {
            $data = $data->where('nipd', 'LIKE', "%{$query['nipd']}%");
        }

        if (isset($query['name'])) {
            $data = $data->where('name_student', 'LIKE', "%{$query['name']}%");
        }

        if (isset($query['birth'])) {
            $data = $data->whereYear('date_birth', $query['birth']);
        }

        if (isset($query['limit']) && isset($query['page'])) {
            $query['page'] = $query['page'] <= 1 ? $query['page'] = 0 : $query['page'];
            $data = $data->skip($query['page'] * $query['limit'])->take($query['limit']);
        }


        return response()->json([
            'data' => $data->get(),
            'count' => $count
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function check_spp_transaction(Request $request, $nisn)
    {
        $params_query = $request->query();
        $siswa = SiswaModel::find($nisn);
        if ($siswa) {
            if (isset($params_query['paid_off'])) {
                return response()->json([
                    'data' => $siswa->spp_transaction->where('paid_off', $params_query['paid_off']),
                    'count' => $siswa->spp_transaction->where('paid_off', $params_query['paid_off'])->count(),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get spp transaction'
                ]);
            } else if (isset($params_query['year'])) {
                return response()->json([
                    'data' =>  $siswa[0]->spp_transaction->where('study_year_id', $params_query['year']),
                    'count' =>  $siswa[0]->spp_transaction->where('study_year_id', $params_query['year'])->count(),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get spp transaction'
                ]);
            }
            return response()->json([
                'data' => $siswa->spp_transaction->sortByDesc('updated_at'),
                'count' => $siswa->spp_transaction->sortByDesc('updated_at')->count(),
                'code' => Response::HTTP_OK,
                'error' => false,
                'message' => 'Success full get spp transaction'
            ]);
        }
        return response()->json([
            'siswa' => $siswa,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }


    public function check_du_transaction(Request $request, $nisn)
    {
        $params_query = $request->query();
        $siswa = SiswaModel::find($nisn);

        if ($siswa) {
            if (isset($params_query['paid_off'])) {
                return response()->json([
                    'data' => $siswa->du_transaction->where('paid_off', $params_query['paid_off']),
                    'count' => $siswa->du_transaction->where('paid_off', $params_query['paid_off'])->count(),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get du transaction'
                ]);
                return;
            } else if (isset($params_query['year'])) {
                return response()->json([
                    'data' =>  $siswa[0]->du_transaction->where('study_year_id', $params_query['year']),
                    'count' =>  $siswa[0]->du_transaction->where('study_year_id', $params_query['year'])->count(),
                    'code' => Response::HTTP_OK,
                    'error' => false,
                    'message' => 'Success full get du transaction'
                ]);
            }
            return response()->json([
                'data' => $siswa->du_transaction->sortByDesc('updated_at'),
                'count' => $siswa->du_transaction->sortByDesc('updated_at')->count(),
                'code' => Response::HTTP_OK,
                'error' => false,
                'message' => 'Success full get du transaction'
            ]);
        }
        return response()->json([
            'data' => $siswa,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validation = $this->student->validation_input_siswa($request);
            if ($validation['error']) {
                return response()->json([
                    $validation
                ], Response::HTTP_NOT_ACCEPTABLE);
            }
            $regis = SiswaModel::create($request->all());
            return response()->json([
                'data' => $regis,
                'code' => 200,
                'error' => false,
                'message' => 'User has been created'
            ]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'data' => $regis,
                'code' => 200,
                'error' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($nisn)
    {
        $userIsExist = SiswaModel::check_user($nisn);
        $student = SiswaModel::find($nisn);
        if ($userIsExist) {
            return response()->json([
                'data' => $student,
                'code' => 200,
                'error' => false,
                'message' => 'User is successfull'
            ]);
        }

        return response()->json([
            'data' => $student,
            'code' => 404,
            'error' => true,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validate = $this->student->validation_edit_siswa($request);
        if ($validate['error']) {
            return response()->json([
                $validate
            ], Response::HTTP_NOT_ACCEPTABLE);
        }

        $userIsExist = SiswaModel::check_user($id);
        $student = SiswaModel::find($id);
        if ($userIsExist) {
            $student->update($request->all());
            return response()->json([
                'data' => $student,
                'error' => false,
                'code' => 200,
                'message' => 'Data has been updated'
            ]);
        }
        return response()->json([
            'data' => $student,
            'error' => true,
            'code' => Response::HTTP_NOT_FOUND,
            'message' => 'User not found'
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Update the students classs.
     *
     * !! Update class not yet fixed, I'm still looking for the solution
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update_students_class_year(Request $request)
    {
        $query = $request->query();
        if (isset($query)) {
            $students = DB::table('siswa')->where('kelas', "LIKE", "%{$query['kelas']}%")->get();
            $kelas = [];
            $students->update([
                'kelas' => 8
            ])->exec();
            foreach ($students as $key => $value) {
                # code...
                $separate_code_class = explode('.', $value->kelas);
                array_push($kelas, $separate_code_class[1]);
            }
            return $students;
        }

        return response()->json([
            'message' => 'Value class not found',
            'error' => true,
            'code' => Response::HTTP_BAD_REQUEST
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($nisn)
    {
        try {
            if (SiswaModel::check_user($nisn)) {
                $siswa = SiswaModel::find($nisn);
                $siswa->delete();
                return response()->json([
                    'message' => 'Data has been deleted',
                    'error' => false,
                    'code' => Response::HTTP_OK
                ]);
            }

            return response()->json([
                'message' => 'User not found',
                'error' => true,
                'code' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'error' => true,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
