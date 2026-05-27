<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_trigger_lease_dates()
            RETURNS trigger AS $$
            DECLARE
                expected_end date;
            BEGIN
                IF NEW.duration_month IS NULL OR NEW.duration_month < 3 OR NEW.duration_month > 12 THEN
                    RAISE EXCEPTION 'Lease duration must be between 3 and 12 months.';
                END IF;

                expected_end := (NEW.date_start + (NEW.duration_month || ' months')::interval - INTERVAL '1 day')::date;

                IF NEW.date_end <> expected_end THEN
                    RAISE EXCEPTION 'Lease end date must match start date plus duration minus one day.';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_lease_dates ON leases;
            CREATE TRIGGER trg_lease_dates
            BEFORE INSERT OR UPDATE OF date_start, date_end, duration_month ON leases
            FOR EACH ROW EXECUTE FUNCTION fn_trigger_lease_dates();

            CREATE OR REPLACE FUNCTION fn_trigger_lease_retention()
            RETURNS trigger AS $$
            BEGIN
                IF OLD.date_end IS NULL OR OLD.date_end > CURRENT_DATE - INTERVAL '3 years' THEN
                    RAISE EXCEPTION 'Lease records must be retained for at least three years after expiry.';
                END IF;

                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_lease_retention ON leases;
            CREATE TRIGGER trg_lease_retention
            BEFORE DELETE ON leases
            FOR EACH ROW EXECUTE FUNCTION fn_trigger_lease_retention();

            CREATE OR REPLACE FUNCTION fn_trigger_property_retention()
            RETURNS trigger AS $$
            BEGIN
                IF OLD.is_available = false AND OLD.updated_at > CURRENT_TIMESTAMP - INTERVAL '3 years' THEN
                    RAISE EXCEPTION 'Withdrawn property records must be retained for at least three years.';
                END IF;

                RETURN OLD;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_property_retention ON properties;
            CREATE TRIGGER trg_property_retention
            BEFORE DELETE ON properties
            FOR EACH ROW EXECUTE FUNCTION fn_trigger_property_retention();
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_property_retention ON properties;
            DROP FUNCTION IF EXISTS fn_trigger_property_retention();
            DROP TRIGGER IF EXISTS trg_lease_retention ON leases;
            DROP FUNCTION IF EXISTS fn_trigger_lease_retention();
            DROP TRIGGER IF EXISTS trg_lease_dates ON leases;
            DROP FUNCTION IF EXISTS fn_trigger_lease_dates();
        ");
    }
};
