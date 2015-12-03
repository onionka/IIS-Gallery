<?php
/**
 * Created by PhpStorm.
 * User: xcibul10
 * Date: 12/1/15
 * Time: 2:58 PM
 */

namespace App\Http\Controllers\Inserters;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;


class EmployeeInsertController extends Controller {

    function __construct()
    {
        $this->middleware("admin");
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'password' => 'required|min:6|max:60',
            'email' => 'required|email|unique:users,email|max:255',
            'birthno' => 'required|unique:Zamestnanec,rc|max:10',
            'firstname' => 'required|max:20',
            'lastname' => 'required|max:20',
            'phone' => 'max:16',
            'role' => 'required|in:employee,admin'
        ]);
    }

    function getInsert()
    {
        return view("admin.insert")
            ->with("table", DB::select(
                "SELECT name, email, password, rc, meno, priezvisko, telefon, role
                    FROM users INNER JOIN Zamestnanec
                        WHERE users.id = Zamestnanec.IDzamestnanca"
            ))->with(
                "header", ["name", "email", "password", "birthno", "firstname", "lastname", "phone", "role"]
            )->with("target", "/man_employee/insert");
    }

    function postInsert(Request $request)
    {
        $user_tags = [
            'name' => $request->request->get("username"),
            'email' => $request->request->get("email"),
            'password' => bcrypt($request->request->get("password"))
        ];

        if ($request->request->get("role") == "")
            $request->request->set("role", "employee");

        $validator = $this->validator($request->request->all());
        if ($validator->fails())
            $this->throwValidationException($request, $validator);

        $user = User::create($user_tags);

        $zam_tags = [
            "IDzamestnanca" => $user->id,
            "rc" => $request->request->get("birthno"),
            "meno" => $request->request->get("firstname"),
            "priezvisko" => $request->request->get("lastname"),
            "telefon" => $request->request->get("phone")
        ];

        DB::table("users")->where(["id" => $user->id])->update(["role" => $request->request->get("role")]);
        DB::table("Zamestnanec")->insert($zam_tags);
        return redirect("/man_employee/insert");
    }

    function getDelete()
    {
        return view("admin.delete")
            ->with("table", DB::select(
                "SELECT id, name username, email, password, rc, meno, priezvisko, telefon, role
                    FROM users INNER JOIN Zamestnanec
                        WHERE users.id = Zamestnanec.IDzamestnanca"
            ))->with(
                "header", ["username", "email", "password", "birthno", "firstname", "lastname", "phone", "role"]
            )->with("target", "/man_employee/delete");
    }

    function postDelete(Request $request)
    {
        foreach ($request->request->keys() as $key)
            if ($request->request->get($key) == "delete")
            {
                DB::table("users")->where(["id" => $key])->delete();
            }
        return redirect('/man_employee/delete');
    }
};