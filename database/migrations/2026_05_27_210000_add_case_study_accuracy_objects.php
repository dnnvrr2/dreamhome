<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->char('staff_no', 5)->nullable()->after('branch_no');
            $table->foreign('staff_no')->references('staff_no')->on('staff')->nullOnDelete();
            $table->index('staff_no', 'idx_properties_staff');
        });

        DB::statement("
            UPDATE properties p
            SET staff_no = s.staff_no
            FROM staff s
            WHERE p.staff_no IS NULL
              AND p.branch_no = s.branch_no
              AND s.staff_no = (
                  SELECT s2.staff_no
                  FROM staff s2
                  WHERE s2.branch_no = p.branch_no
                  ORDER BY s2.staff_no
                  LIMIT 1
              )
        ");

        Schema::create('newspapers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('street', 60)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('tel_no', 20)->nullable();
            $table->string('contact_name', 80)->nullable();
            $table->timestamps();
        });

        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->char('property_no', 5);
            $table->foreignId('newspaper_id')->constrained('newspapers')->cascadeOnDelete();
            $table->date('advert_date');
            $table->decimal('cost', 8, 2)->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('property_no')->references('property_no')->on('properties')->cascadeOnDelete();
            $table->index('property_no', 'idx_adverts_property');
            $table->index('newspaper_id', 'idx_adverts_newspaper');
        });

        DB::unprepared("
            CREATE OR REPLACE FUNCTION fn_trigger_staff_property_limit()
            RETURNS trigger AS $$
            DECLARE
                managed_count integer;
            BEGIN
                IF NEW.staff_no IS NULL THEN
                    RETURN NEW;
                END IF;

                SELECT COUNT(*)
                INTO managed_count
                FROM properties
                WHERE staff_no = NEW.staff_no
                  AND property_no <> NEW.property_no;

                IF managed_count >= 20 THEN
                    RAISE EXCEPTION 'A staff member may manage a maximum of 20 properties.';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            DROP TRIGGER IF EXISTS trg_staff_property_limit ON properties;
            CREATE TRIGGER trg_staff_property_limit
            BEFORE INSERT OR UPDATE OF staff_no ON properties
            FOR EACH ROW EXECUTE FUNCTION fn_trigger_staff_property_limit();

            DROP VIEW IF EXISTS vw_available_properties;
            CREATE VIEW vw_available_properties AS
            SELECT p.property_no,
                   p.street,
                   p.area,
                   p.city,
                   p.postcode,
                   p.type,
                   p.rooms,
                   p.rent,
                   p.staff_no,
                   s.f_name AS staff_fname,
                   s.l_name AS staff_lname,
                   o.f_name AS owner_fname,
                   o.l_name AS owner_lname,
                   o.tel_no AS owner_tel,
                   b.branch_no,
                   b.city AS branch_city
            FROM properties p
            LEFT JOIN owners o ON p.owner_no = o.owner_no
            LEFT JOIN branches b ON p.branch_no = b.branch_no
            LEFT JOIN staff s ON p.staff_no = s.staff_no
            WHERE p.is_available = true
            ORDER BY p.rent;

            CREATE OR REPLACE VIEW vw_property_adverts AS
            SELECT a.id,
                   a.property_no,
                   p.street AS property_street,
                   p.city AS property_city,
                   a.newspaper_id,
                   n.name AS newspaper_name,
                   a.advert_date,
                   a.cost,
                   a.comments
            FROM adverts a
            JOIN properties p ON a.property_no = p.property_no
            JOIN newspapers n ON a.newspaper_id = n.id;

            CREATE OR REPLACE VIEW vw_properties_due_inspection AS
            SELECT p.property_no,
                   p.street,
                   p.city,
                   p.branch_no,
                   p.staff_no,
                   MAX(i.inspection_date) AS last_inspection_date
            FROM properties p
            LEFT JOIN inspections i ON p.property_no = i.property_no
            WHERE p.is_available = true
            GROUP BY p.property_no, p.street, p.city, p.branch_no, p.staff_no
            HAVING MAX(i.inspection_date) IS NULL
                OR MAX(i.inspection_date) < CURRENT_DATE - INTERVAL '6 months';

            DROP PROCEDURE IF EXISTS sp_add_property(
                character varying,
                character varying,
                character varying,
                character varying,
                smallint,
                numeric,
                character varying,
                character varying
            );

            CREATE PROCEDURE sp_add_property(
                IN p_property_no character varying,
                IN p_street character varying,
                IN p_city character varying,
                IN p_type character varying,
                IN p_rooms smallint,
                IN p_rent numeric,
                IN p_owner_no character varying,
                IN p_branch_no character varying,
                IN p_staff_no character varying DEFAULT NULL
            )
            LANGUAGE plpgsql
            AS $$
            BEGIN
                INSERT INTO properties
                    (property_no, street, city, type, rooms, rent, owner_no, branch_no, staff_no, created_at, updated_at)
                VALUES
                    (p_property_no, p_street, p_city, p_type, p_rooms, p_rent, p_owner_no, p_branch_no, p_staff_no, NOW(), NOW());
            END;
            $$;
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP VIEW IF EXISTS vw_properties_due_inspection;
            DROP VIEW IF EXISTS vw_property_adverts;
            DROP TRIGGER IF EXISTS trg_staff_property_limit ON properties;
            DROP FUNCTION IF EXISTS fn_trigger_staff_property_limit();
        ");

        Schema::dropIfExists('adverts');
        Schema::dropIfExists('newspapers');

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['staff_no']);
            $table->dropIndex('idx_properties_staff');
            $table->dropColumn('staff_no');
        });
    }
};
