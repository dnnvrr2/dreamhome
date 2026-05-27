<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE UNIQUE INDEX IF NOT EXISTS uniq_branch_manager
            ON staff (branch_no)
            WHERE position = 'Manager' AND branch_no IS NOT NULL;

            CREATE OR REPLACE FUNCTION fn_trigger_supervisor_rules()
            RETURNS trigger AS $$
            DECLARE
                supervisor_position text;
                supervised_count integer;
            BEGIN
                IF NEW.supervisor_no IS NULL THEN
                    RETURN NEW;
                END IF;

                SELECT position
                INTO supervisor_position
                FROM staff
                WHERE staff_no = NEW.supervisor_no;

                IF supervisor_position IS DISTINCT FROM 'Supervisor' THEN
                    RAISE EXCEPTION 'Assigned supervisor must have the Supervisor position.';
                END IF;

                SELECT COUNT(*)
                INTO supervised_count
                FROM staff
                WHERE supervisor_no = NEW.supervisor_no
                  AND staff_no <> NEW.staff_no;

                IF supervised_count >= 10 THEN
                    RAISE EXCEPTION 'A supervisor may supervise a maximum of 10 staff members.';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_supervisor_rules ON staff;
            CREATE TRIGGER trg_supervisor_rules
            BEFORE INSERT OR UPDATE OF supervisor_no ON staff
            FOR EACH ROW EXECUTE FUNCTION fn_trigger_supervisor_rules();

            CREATE OR REPLACE VIEW vw_supervisor_workload AS
            SELECT sup.staff_no AS supervisor_no,
                   sup.f_name AS supervisor_fname,
                   sup.l_name AS supervisor_lname,
                   sup.branch_no,
                   COUNT(s.staff_no) AS supervised_staff_count
            FROM staff sup
            LEFT JOIN staff s ON s.supervisor_no = sup.staff_no
            WHERE sup.position = 'Supervisor'
            GROUP BY sup.staff_no, sup.f_name, sup.l_name, sup.branch_no;
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP VIEW IF EXISTS vw_supervisor_workload;
            DROP TRIGGER IF EXISTS trg_supervisor_rules ON staff;
            DROP FUNCTION IF EXISTS fn_trigger_supervisor_rules();
            DROP INDEX IF EXISTS uniq_branch_manager;
        ");
    }
};
