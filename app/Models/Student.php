<?php

namespace App\Models;

use App\Http\Controllers\GenController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Student extends Model
{
  use HasFactory;
  public $timestamps = false;

  public static function valid($data, $is_req = true)
  {
    $rules = [
      'user_id' => 'required|numeric',
      'student_number' => 'nullable|min:2|max:15',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id)
  {
    return 'A-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req)
  {
    $program_id = (int) $req->program_id;

    $students = Student::query()
      ->join('users', 'students.user_id', 'users.id')
      ->where('users.is_active', $req->is_active);

    if ($program_id != 0) {
      $students->join('student_programs', 'student_programs.student_id', 'students.id')
        ->where([
          ['student_programs.program_id', $program_id],
          ['student_programs.is_active', 1],
        ]);
    }

    $students = $students
      ->orderBy('name')
      ->orderBy('surname_p')
      ->orderBy('surname_m')
      ->get([
        'students.id',
        'students.user_id',
        'users.is_active',
        'users.name',
        'users.surname_p',
        'users.surname_m',
        'users.curp',
        'users.email',
      ]);

    foreach ($students as $key => $student) {
      $student->key = $key;
      $student->full_name = GenController::getFullName($student);

      $student_programs = StudentProgram::query()
        ->where([
          ['student_id', $student->id],
          ['is_active', 1],
        ])
        ->get('program_id');

      $student->lab_programs = '';
      foreach ($student_programs as $key => $student_program) {
        $program = Program::find($student_program->program_id, ['name', 'code']);

        $student->lab_programs .= $program->name . " | " . $program->code . "\n";
      }
    }

    return $students;
  }

  static public function getItem($req, $id)
  {
    $item = Student::find($id, [
      'id',
      'user_id',
      'student_number',
    ]);

    $item->user = User::getItem(null, $item->user_id);
    $item->student_degrees = StudentDegree::getItems($id, 1);
    $item->student_documents = StudentDocument::getItems($id, 1);
    $item->student_programs = StudentProgram::getItems($id, 1);

    return $item;
  }
}
