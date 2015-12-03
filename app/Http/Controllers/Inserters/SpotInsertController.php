<?php
/**
 * Created by PhpStorm.
 * User: onionka
 * Date: 12/1/15
 * Time: 2:58 PM
 */

namespace App\Http\Controllers\Inserters;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\User;
use Validator;


class SpotInsertController extends Controller {

    function __construct()
    {
        $this->middleware("admin");
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'spottype' => 'required|max: 20',
            'area' => 'required|numeric',
            'cost' => 'required|numeric',
            'room' => 'required|exists:Miestnost,IDmiestnosti',
            'responsibility' => 'exists:Zamestnanec,IDzamestnanca',
        ]);
    }

    function getSpot()
    {
        $sql = "SELECT typ, velkost, cena, IDmiestnosti, IDzamestnanca
                FROM ExpozicneMiesto";
        $rooms = "SELECT IDmiestnosti id FROM Miestnost";
        $employees = "SELECT IDzamestnanca id, meno name, priezvisko surname FROM Zamestnanec";
        return view("admin.insert")
            ->with("table", DB::select($sql))
            ->with("rooms", DB::select($rooms))
            ->with("employees", DB::select($employees))
            ->with("header", ["spot type", "area", "cost", "room", "responsibility"])
            ->with("target", "/man_spot/send");
    }

    function postSend(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $responsible = $request->request->get("responsibility");
        if($responsible == "")
            $responsible = null;

        DB::table("ExpozicneMiesto")->insert([
            "typ" => $request->request->get("spottype"),
            "velkost" => $request->request->get("area"),
            "cena" => $request->request->get("cost"),
            "IDmiestnosti" => $request->request->get("room"),
            "IDzamestnanca" => $responsible,
        ]);

        return redirect("/man_spot/spot");
    }
};