<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdvertController extends Controller
{
    public function index(Request $request)
    {
        $propertyNo = $request->property_no;
        $newspaperId = $request->newspaper_id;

        $sql = "
            SELECT *
            FROM vw_property_adverts
            WHERE 1=1
        ";

        $params = [];

        if ($propertyNo) {
            $sql .= " AND property_no = ?";
            $params[] = $propertyNo;
        }

        if ($newspaperId) {
            $sql .= " AND newspaper_id = ?";
            $params[] = $newspaperId;
        }

        $sql .= " ORDER BY advert_date DESC, id DESC";

        $adverts = DB::select($sql, $params);
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $newspapers = DB::select("SELECT * FROM newspapers ORDER BY name");

        return view('adverts.index', compact('adverts', 'properties', 'newspapers'));
    }

    public function create()
    {
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $newspapers = DB::select("SELECT * FROM newspapers ORDER BY name");

        return view('adverts.create', compact('properties', 'newspapers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_no' => 'required|exists:properties,property_no',
            'advert_date' => 'required|date',
            'newspaper_id' => 'nullable|exists:newspapers,id',
            'newspaper_name' => 'required_without:newspaper_id|max:80',
            'cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $newspaperId = $this->resolveNewspaperId($request);

            DB::insert("
                INSERT INTO adverts
                    (property_no, newspaper_id, advert_date, cost, comments, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ", [
                $request->property_no,
                $newspaperId,
                $request->advert_date,
                $request->cost,
                $request->comments,
            ]);
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('adverts.index')
            ->with('success', 'Advert recorded successfully.');
    }

    public function edit($id)
    {
        $advert = DB::selectOne("SELECT * FROM adverts WHERE id = ?", [$id]) ?? abort(404);
        $properties = DB::select("SELECT * FROM properties ORDER BY property_no");
        $newspapers = DB::select("SELECT * FROM newspapers ORDER BY name");

        return view('adverts.edit', compact('advert', 'properties', 'newspapers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'property_no' => 'required|exists:properties,property_no',
            'advert_date' => 'required|date',
            'newspaper_id' => 'nullable|exists:newspapers,id',
            'newspaper_name' => 'required_without:newspaper_id|max:80',
            'cost' => 'nullable|numeric|min:0',
        ]);

        try {
            $newspaperId = $this->resolveNewspaperId($request);

            DB::update("
                UPDATE adverts
                SET property_no = ?,
                    newspaper_id = ?,
                    advert_date = ?,
                    cost = ?,
                    comments = ?,
                    updated_at = NOW()
                WHERE id = ?
            ", [
                $request->property_no,
                $newspaperId,
                $request->advert_date,
                $request->cost,
                $request->comments,
                $id,
            ]);
        } catch (QueryException $exception) {
            return back()->withInput()->with('error', $this->databaseError($exception));
        }

        return redirect()->route('adverts.index')
            ->with('success', 'Advert updated successfully.');
    }

    public function destroy($id)
    {
        DB::delete("DELETE FROM adverts WHERE id = ?", [$id]);

        return redirect()->route('adverts.index')
            ->with('success', 'Advert deleted successfully.');
    }

    private function resolveNewspaperId(Request $request): int
    {
        if ($request->newspaper_id) {
            return (int) $request->newspaper_id;
        }

        $name = trim($request->newspaper_name);

        $existing = DB::selectOne("SELECT id FROM newspapers WHERE LOWER(name) = LOWER(?)", [$name]);

        if ($existing) {
            return (int) $existing->id;
        }

        return (int) DB::table('newspapers')->insertGetId([
            'name' => $name,
            'street' => $request->newspaper_street,
            'city' => $request->newspaper_city,
            'postcode' => $request->newspaper_postcode,
            'tel_no' => $request->newspaper_tel_no,
            'contact_name' => $request->newspaper_contact_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
