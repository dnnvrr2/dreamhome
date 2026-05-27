--
-- PostgreSQL database dump
--

\restrict NfjHzF1jDxe6JSBAnE4YxZ6SlWTWOgDVEfQbc8Txefn9UwfPCnKT197X92R4Gbg

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE IF EXISTS dreamhome;
--
-- Name: dreamhome; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE dreamhome WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'English_United States.1252';


ALTER DATABASE dreamhome OWNER TO postgres;

\unrestrict NfjHzF1jDxe6JSBAnE4YxZ6SlWTWOgDVEfQbc8Txefn9UwfPCnKT197X92R4Gbg
\connect dreamhome
\restrict NfjHzF1jDxe6JSBAnE4YxZ6SlWTWOgDVEfQbc8Txefn9UwfPCnKT197X92R4Gbg

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: fn_count_properties_by_branch(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_count_properties_by_branch(p_branch_no character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM properties
    WHERE branch_no = p_branch_no;
    RETURN v_count;
END;
$$;


ALTER FUNCTION public.fn_count_properties_by_branch(p_branch_no character varying) OWNER TO postgres;

--
-- Name: fn_count_staff_by_position(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_count_staff_by_position(p_position character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_count INTEGER;
BEGIN
    SELECT COUNT(*) INTO v_count
    FROM staff
    WHERE position = p_position;
    RETURN v_count;
END;
$$;


ALTER FUNCTION public.fn_count_staff_by_position(p_position character varying) OWNER TO postgres;

--
-- Name: fn_is_property_available(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_is_property_available(p_property_no character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_available BOOLEAN;
BEGIN
    SELECT is_available INTO v_available
    FROM properties
    WHERE property_no = p_property_no;
    RETURN COALESCE(v_available, false);
END;
$$;


ALTER FUNCTION public.fn_is_property_available(p_property_no character varying) OWNER TO postgres;

--
-- Name: fn_lease_duration_days(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_lease_duration_days(p_lease_no character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_days INTEGER;
BEGIN
    SELECT (date_end - date_start) INTO v_days
    FROM leases
    WHERE lease_no = p_lease_no;
    RETURN COALESCE(v_days, 0);
END;
$$;


ALTER FUNCTION public.fn_lease_duration_days(p_lease_no character varying) OWNER TO postgres;

--
-- Name: fn_total_rent_by_branch(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_total_rent_by_branch(p_branch_no character varying) RETURNS numeric
    LANGUAGE plpgsql
    AS $$
DECLARE
    v_total DECIMAL(10,2);
BEGIN
    SELECT COALESCE(SUM(rent), 0) INTO v_total
    FROM properties
    WHERE branch_no = p_branch_no
    AND is_available = true;
    RETURN v_total;
END;
$$;


ALTER FUNCTION public.fn_total_rent_by_branch(p_branch_no character varying) OWNER TO postgres;

--
-- Name: fn_trigger_lease_dates(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_lease_dates() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
            $$;


ALTER FUNCTION public.fn_trigger_lease_dates() OWNER TO postgres;

--
-- Name: fn_trigger_lease_retention(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_lease_retention() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF OLD.date_end IS NULL OR OLD.date_end > CURRENT_DATE - INTERVAL '3 years' THEN
                    RAISE EXCEPTION 'Lease records must be retained for at least three years after expiry.';
                END IF;

                RETURN OLD;
            END;
            $$;


ALTER FUNCTION public.fn_trigger_lease_retention() OWNER TO postgres;

--
-- Name: fn_trigger_minimum_rent(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_minimum_rent() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF NEW.rent < 100 THEN
        RAISE EXCEPTION 'Rent cannot be below 100. Provided: %', NEW.rent;
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_trigger_minimum_rent() OWNER TO postgres;

--
-- Name: fn_trigger_prevent_lease_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_prevent_lease_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF OLD.date_end >= CURRENT_DATE THEN
        RAISE EXCEPTION 'Cannot delete an active lease. Lease % is still active.', OLD.lease_no;
    END IF;
    RETURN OLD;
END;
$$;


ALTER FUNCTION public.fn_trigger_prevent_lease_delete() OWNER TO postgres;

--
-- Name: fn_trigger_property_retention(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_property_retention() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                IF OLD.is_available = false AND OLD.updated_at > CURRENT_TIMESTAMP - INTERVAL '3 years' THEN
                    RAISE EXCEPTION 'Withdrawn property records must be retained for at least three years.';
                END IF;

                RETURN OLD;
            END;
            $$;


ALTER FUNCTION public.fn_trigger_property_retention() OWNER TO postgres;

--
-- Name: fn_trigger_property_status(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_property_status() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF OLD.is_available <> NEW.is_available THEN
        INSERT INTO property_audit_log (property_no, old_status, new_status)
        VALUES (NEW.property_no, OLD.is_available, NEW.is_available);
    END IF;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_trigger_property_status() OWNER TO postgres;

--
-- Name: fn_trigger_staff_property_limit(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_staff_property_limit() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
            $$;


ALTER FUNCTION public.fn_trigger_staff_property_limit() OWNER TO postgres;

--
-- Name: fn_trigger_staff_updated_at(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_staff_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.fn_trigger_staff_updated_at() OWNER TO postgres;

--
-- Name: fn_trigger_supervisor_rules(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.fn_trigger_supervisor_rules() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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
            $$;


ALTER FUNCTION public.fn_trigger_supervisor_rules() OWNER TO postgres;

--
-- Name: sp_add_property(character varying, character varying, character varying, character varying, smallint, numeric, character varying, character varying, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.sp_add_property(IN p_property_no character varying, IN p_street character varying, IN p_city character varying, IN p_type character varying, IN p_rooms smallint, IN p_rent numeric, IN p_owner_no character varying, IN p_branch_no character varying, IN p_staff_no character varying DEFAULT NULL::character varying)
    LANGUAGE plpgsql
    AS $$
            BEGIN
                INSERT INTO properties
                    (property_no, street, city, type, rooms, rent, owner_no, branch_no, staff_no, created_at, updated_at)
                VALUES
                    (p_property_no, p_street, p_city, p_type, p_rooms, p_rent, p_owner_no, p_branch_no, p_staff_no, NOW(), NOW());
            END;
            $$;


ALTER PROCEDURE public.sp_add_property(IN p_property_no character varying, IN p_street character varying, IN p_city character varying, IN p_type character varying, IN p_rooms smallint, IN p_rent numeric, IN p_owner_no character varying, IN p_branch_no character varying, IN p_staff_no character varying) OWNER TO postgres;

--
-- Name: sp_record_inspection(character varying, character varying, date, text); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.sp_record_inspection(IN p_property_no character varying, IN p_staff_no character varying, IN p_date date, IN p_comments text)
    LANGUAGE plpgsql
    AS $$
BEGIN
    INSERT INTO inspections (
        property_no, staff_no, inspection_date, comments,
        created_at, updated_at
    ) VALUES (
        p_property_no, p_staff_no, p_date, p_comments, NOW(), NOW()
    );
    RAISE NOTICE 'Inspection recorded for property %.', p_property_no;
END;
$$;


ALTER PROCEDURE public.sp_record_inspection(IN p_property_no character varying, IN p_staff_no character varying, IN p_date date, IN p_comments text) OWNER TO postgres;

--
-- Name: sp_transfer_property(character varying, character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.sp_transfer_property(IN p_property_no character varying, IN p_new_branch_no character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE properties
    SET branch_no = p_new_branch_no, updated_at = NOW()
    WHERE property_no = p_property_no;
    RAISE NOTICE 'Property % transferred to branch %.', p_property_no, p_new_branch_no;
END;
$$;


ALTER PROCEDURE public.sp_transfer_property(IN p_property_no character varying, IN p_new_branch_no character varying) OWNER TO postgres;

--
-- Name: sp_update_rent(character varying, numeric); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.sp_update_rent(IN p_property_no character varying, IN p_new_rent numeric)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE properties
    SET rent = p_new_rent, updated_at = NOW()
    WHERE property_no = p_property_no;
    RAISE NOTICE 'Rent updated for property %.', p_property_no;
END;
$$;


ALTER PROCEDURE public.sp_update_rent(IN p_property_no character varying, IN p_new_rent numeric) OWNER TO postgres;

--
-- Name: sp_withdraw_property(character varying); Type: PROCEDURE; Schema: public; Owner: postgres
--

CREATE PROCEDURE public.sp_withdraw_property(IN p_property_no character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE properties
    SET is_available = false, updated_at = NOW()
    WHERE property_no = p_property_no;
    RAISE NOTICE 'Property % has been withdrawn.', p_property_no;
END;
$$;


ALTER PROCEDURE public.sp_withdraw_property(IN p_property_no character varying) OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: adverts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.adverts (
    id bigint NOT NULL,
    property_no character(5) NOT NULL,
    newspaper_id bigint NOT NULL,
    advert_date date NOT NULL,
    cost numeric(8,2),
    comments text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.adverts OWNER TO postgres;

--
-- Name: adverts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.adverts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.adverts_id_seq OWNER TO postgres;

--
-- Name: adverts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.adverts_id_seq OWNED BY public.adverts.id;


--
-- Name: branches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.branches (
    branch_no character(4) NOT NULL,
    street character varying(60) NOT NULL,
    area character varying(40),
    city character varying(30) NOT NULL,
    postcode character varying(10) NOT NULL,
    tel_no character varying(20),
    fax_no character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.branches OWNER TO postgres;

--
-- Name: client_requests; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.client_requests (
    id bigint NOT NULL,
    property_no character(5),
    f_name character varying(30) NOT NULL,
    l_name character varying(40) NOT NULL,
    email character varying(120),
    tel_no character varying(20) NOT NULL,
    street character varying(60),
    area character varying(30),
    city character varying(30),
    postcode character varying(10),
    pref_type character varying(20),
    max_rent numeric(8,2),
    preferred_view_date date,
    comments text,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    approved_client_no character(5),
    processed_by bigint,
    processed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.client_requests OWNER TO postgres;

--
-- Name: client_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.client_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.client_requests_id_seq OWNER TO postgres;

--
-- Name: client_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.client_requests_id_seq OWNED BY public.client_requests.id;


--
-- Name: clients; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.clients (
    client_no character(5) NOT NULL,
    f_name character varying(30) NOT NULL,
    l_name character varying(40) NOT NULL,
    street character varying(60),
    area character varying(30),
    city character varying(30),
    postcode character varying(10),
    tel_no character varying(20),
    pref_type character varying(20),
    max_rent numeric(8,2),
    comments text,
    registered_by character(5),
    branch_no character(4),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.clients OWNER TO postgres;

--
-- Name: inspections; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.inspections (
    inspection_id integer NOT NULL,
    property_no character(5),
    staff_no character(5),
    inspection_date date NOT NULL,
    comments text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.inspections OWNER TO postgres;

--
-- Name: inspections_inspection_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.inspections_inspection_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.inspections_inspection_id_seq OWNER TO postgres;

--
-- Name: inspections_inspection_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.inspections_inspection_id_seq OWNED BY public.inspections.inspection_id;


--
-- Name: leases; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.leases (
    lease_no character(5) NOT NULL,
    monthly_rent numeric(8,2),
    payment_method character varying(40),
    deposit numeric(8,2),
    deposit_paid boolean DEFAULT false NOT NULL,
    date_start date,
    date_end date,
    duration_month smallint,
    client_no character(5),
    property_no character(5),
    staff_no character(5),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.leases OWNER TO postgres;

--
-- Name: managers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.managers (
    staff_no character(5) NOT NULL,
    date_start date,
    car_allowance numeric(8,2),
    bonus numeric(8,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.managers OWNER TO postgres;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: newspapers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.newspapers (
    id bigint NOT NULL,
    name character varying(80) NOT NULL,
    street character varying(60),
    city character varying(30),
    postcode character varying(10),
    tel_no character varying(20),
    contact_name character varying(80),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.newspapers OWNER TO postgres;

--
-- Name: newspapers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.newspapers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.newspapers_id_seq OWNER TO postgres;

--
-- Name: newspapers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.newspapers_id_seq OWNED BY public.newspapers.id;


--
-- Name: next_of_kins; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.next_of_kins (
    staff_no character(5) NOT NULL,
    full_name character varying(60) NOT NULL,
    relationship character varying(30),
    street character varying(60),
    city character varying(30),
    tel_no character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.next_of_kins OWNER TO postgres;

--
-- Name: owners; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.owners (
    owner_no character(5) NOT NULL,
    f_name character varying(30) NOT NULL,
    l_name character varying(30) NOT NULL,
    street character varying(60),
    city character varying(30),
    postcode character varying(10),
    tel_no character varying(20),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.owners OWNER TO postgres;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: properties; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.properties (
    property_no character(5) NOT NULL,
    street character varying(60) NOT NULL,
    area character varying(40),
    city character varying(30) NOT NULL,
    postcode character varying(10),
    type character varying(20),
    rooms smallint,
    rent numeric(8,2),
    is_available boolean DEFAULT true NOT NULL,
    owner_no character(5),
    branch_no character(4),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    staff_no character(5)
);


ALTER TABLE public.properties OWNER TO postgres;

--
-- Name: property_audit_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.property_audit_log (
    log_id integer NOT NULL,
    property_no character varying(5),
    old_status boolean,
    new_status boolean,
    changed_at timestamp without time zone DEFAULT now(),
    changed_by text DEFAULT CURRENT_USER
);


ALTER TABLE public.property_audit_log OWNER TO postgres;

--
-- Name: property_audit_log_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.property_audit_log_log_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.property_audit_log_log_id_seq OWNER TO postgres;

--
-- Name: property_audit_log_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.property_audit_log_log_id_seq OWNED BY public.property_audit_log.log_id;


--
-- Name: secretaries; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.secretaries (
    staff_no character(5) NOT NULL,
    typing_speed smallint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.secretaries OWNER TO postgres;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: staff; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.staff (
    staff_no character(5) NOT NULL,
    f_name character varying(30) NOT NULL,
    l_name character varying(30) NOT NULL,
    street character varying(60),
    area character varying(30),
    city character varying(30),
    postcode character varying(10),
    tel_no character varying(20),
    sex character(1),
    dob date,
    nin character varying(12),
    "position" character varying(20) NOT NULL,
    salary numeric(9,2),
    date_joined date,
    branch_no character(4),
    supervisor_no character(5),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.staff OWNER TO postgres;

--
-- Name: supervisors; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.supervisors (
    staff_no character(5) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.supervisors OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role character varying(20) DEFAULT 'admin'::character varying NOT NULL,
    CONSTRAINT chk_user_role CHECK (((role)::text = ANY ((ARRAY['admin'::character varying, 'manager'::character varying, 'supervisor'::character varying, 'staff'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: viewings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.viewings (
    client_no character(5) NOT NULL,
    property_no character(5) NOT NULL,
    view_date date NOT NULL,
    staff_no character(5),
    comments text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.viewings OWNER TO postgres;

--
-- Name: vw_available_properties; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_available_properties AS
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
   FROM (((public.properties p
     LEFT JOIN public.owners o ON ((p.owner_no = o.owner_no)))
     LEFT JOIN public.branches b ON ((p.branch_no = b.branch_no)))
     LEFT JOIN public.staff s ON ((p.staff_no = s.staff_no)))
  WHERE (p.is_available = true)
  ORDER BY p.rent;


ALTER VIEW public.vw_available_properties OWNER TO postgres;

--
-- Name: vw_branch_summary; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_branch_summary AS
 SELECT b.branch_no,
    b.city,
    b.tel_no,
    count(p.property_no) AS total_properties,
    count(
        CASE
            WHEN (p.is_available = true) THEN 1
            ELSE NULL::integer
        END) AS available_properties,
    count(
        CASE
            WHEN (p.is_available = false) THEN 1
            ELSE NULL::integer
        END) AS withdrawn_properties,
    round(avg(p.rent), 2) AS avg_rent
   FROM (public.branches b
     LEFT JOIN public.properties p ON ((b.branch_no = p.branch_no)))
  GROUP BY b.branch_no, b.city, b.tel_no
  ORDER BY b.branch_no;


ALTER VIEW public.vw_branch_summary OWNER TO postgres;

--
-- Name: vw_inspection_report; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_inspection_report AS
 SELECT i.inspection_id,
    i.inspection_date,
    i.comments,
    p.property_no,
    p.street,
    p.city,
    p.type,
    s.f_name AS inspector_fname,
    s.l_name AS inspector_lname,
    s."position" AS inspector_position
   FROM ((public.inspections i
     LEFT JOIN public.properties p ON ((i.property_no = p.property_no)))
     LEFT JOIN public.staff s ON ((i.staff_no = s.staff_no)))
  ORDER BY i.inspection_date DESC;


ALTER VIEW public.vw_inspection_report OWNER TO postgres;

--
-- Name: vw_lease_summary; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_lease_summary AS
 SELECT l.lease_no,
    l.monthly_rent,
    l.payment_method,
    l.deposit,
    l.deposit_paid,
    l.date_start,
    l.date_end,
    l.duration_month,
    c.f_name AS client_fname,
    c.l_name AS client_lname,
    c.tel_no AS client_tel,
    p.street AS property_street,
    p.city AS property_city,
    p.type AS property_type,
    s.f_name AS staff_fname,
    s.l_name AS staff_lname
   FROM (((public.leases l
     LEFT JOIN public.clients c ON ((l.client_no = c.client_no)))
     LEFT JOIN public.properties p ON ((l.property_no = p.property_no)))
     LEFT JOIN public.staff s ON ((l.staff_no = s.staff_no)))
  ORDER BY l.date_start DESC;


ALTER VIEW public.vw_lease_summary OWNER TO postgres;

--
-- Name: vw_properties_due_inspection; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_properties_due_inspection AS
 SELECT p.property_no,
    p.street,
    p.city,
    p.branch_no,
    p.staff_no,
    max(i.inspection_date) AS last_inspection_date
   FROM (public.properties p
     LEFT JOIN public.inspections i ON ((p.property_no = i.property_no)))
  WHERE (p.is_available = true)
  GROUP BY p.property_no, p.street, p.city, p.branch_no, p.staff_no
 HAVING ((max(i.inspection_date) IS NULL) OR (max(i.inspection_date) < (CURRENT_DATE - '6 mons'::interval)));


ALTER VIEW public.vw_properties_due_inspection OWNER TO postgres;

--
-- Name: vw_property_adverts; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_property_adverts AS
 SELECT a.id,
    a.property_no,
    p.street AS property_street,
    p.city AS property_city,
    a.newspaper_id,
    n.name AS newspaper_name,
    a.advert_date,
    a.cost,
    a.comments
   FROM ((public.adverts a
     JOIN public.properties p ON ((a.property_no = p.property_no)))
     JOIN public.newspapers n ON ((a.newspaper_id = n.id)));


ALTER VIEW public.vw_property_adverts OWNER TO postgres;

--
-- Name: vw_staff_details; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_staff_details AS
 SELECT s.staff_no,
    s.f_name,
    s.l_name,
    s."position",
    s.salary,
    s.date_joined,
    b.city AS branch_city,
    b.tel_no AS branch_tel,
    sup.f_name AS supervisor_fname,
    sup.l_name AS supervisor_lname
   FROM ((public.staff s
     LEFT JOIN public.branches b ON ((s.branch_no = b.branch_no)))
     LEFT JOIN public.staff sup ON ((s.supervisor_no = sup.staff_no)))
  ORDER BY s.branch_no, s."position";


ALTER VIEW public.vw_staff_details OWNER TO postgres;

--
-- Name: vw_supervisor_workload; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.vw_supervisor_workload AS
 SELECT sup.staff_no AS supervisor_no,
    sup.f_name AS supervisor_fname,
    sup.l_name AS supervisor_lname,
    sup.branch_no,
    count(s.staff_no) AS supervised_staff_count
   FROM (public.staff sup
     LEFT JOIN public.staff s ON ((s.supervisor_no = sup.staff_no)))
  WHERE ((sup."position")::text = 'Supervisor'::text)
  GROUP BY sup.staff_no, sup.f_name, sup.l_name, sup.branch_no;


ALTER VIEW public.vw_supervisor_workload OWNER TO postgres;

--
-- Name: adverts id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adverts ALTER COLUMN id SET DEFAULT nextval('public.adverts_id_seq'::regclass);


--
-- Name: client_requests id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client_requests ALTER COLUMN id SET DEFAULT nextval('public.client_requests_id_seq'::regclass);


--
-- Name: inspections inspection_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inspections ALTER COLUMN inspection_id SET DEFAULT nextval('public.inspections_inspection_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: newspapers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.newspapers ALTER COLUMN id SET DEFAULT nextval('public.newspapers_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: property_audit_log log_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.property_audit_log ALTER COLUMN log_id SET DEFAULT nextval('public.property_audit_log_log_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: adverts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.adverts (id, property_no, newspaper_id, advert_date, cost, comments, created_at, updated_at) FROM stdin;
1	PG004	1	2025-03-10	120.00	Three-room flat advertised for spring renters.	2026-05-27 22:43:11	2026-05-27 22:43:11
2	PG021	1	2025-06-15	150.00	House advert placed before summer viewing period.	2026-05-27 22:43:11	2026-05-27 22:43:11
3	PL001	2	2025-07-05	220.00	London flat advert with parking note.	2026-05-27 22:43:11	2026-05-27 22:43:11
4	PL002	2	2025-07-12	260.00	Family house advert.	2026-05-27 22:43:11	2026-05-27 22:43:11
5	PB002	3	2025-08-20	110.00	Redland flat advert.	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: branches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.branches (branch_no, street, area, city, postcode, tel_no, fax_no, created_at, updated_at) FROM stdin;
B003	163 Main Street	Partick	Glasgow	G11 9QX	0141-339-2178	0141-339-4439	2026-05-27 22:43:11	2026-05-27 22:43:11
B005	22 Deer Road	Cranford	London	SW1 4EH	020-7946-1188	020-7946-1189	2026-05-27 22:43:11	2026-05-27 22:43:11
B007	8 Clifton Road	Clifton	Bristol	BS8 1AA	0117-496-3321	0117-496-3322	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: client_requests; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.client_requests (id, property_no, f_name, l_name, email, tel_no, street, area, city, postcode, pref_type, max_rent, preferred_view_date, comments, status, approved_client_no, processed_by, processed_at, created_at, updated_at) FROM stdin;
1	PB001	Sophie	Evans	sophie.evans@example.com	0117-555-4401	\N	\N	Bristol	\N	House	750.00	2026-06-15	Interested in Clifton house.	pending	\N	\N	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.clients (client_no, f_name, l_name, street, area, city, postcode, tel_no, pref_type, max_rent, comments, registered_by, branch_no, created_at, updated_at) FROM stdin;
CR074	Mike	Ritchie	18 Tain Street	\N	Gourock	PA19 1YQ	01475-392178	House	750.00	Currently living at home with parents. Getting married in August.	SG37 	B003	2026-05-27 22:43:11	2026-05-27 22:43:11
CR056	Aline	Stewart	22 Queen Street	Partick	Glasgow	G11 8AA	0141-555-2201	Flat	500.00	Needs property close to university.	SG014	B003	2026-05-27 22:43:11	2026-05-27 22:43:11
CR062	John	Kay	9 Station Road	Hyndland	Glasgow	G12 8QQ	0141-555-2202	Flat	450.00	Prefers ground floor.	SG014	B003	2026-05-27 22:43:11	2026-05-27 22:43:11
CR083	Louise	Brown	3 Fleet Street	\N	London	EC4Y 1AA	020-5555-2203	Flat	1000.00	Requires central location.	SL41 	B005	2026-05-27 22:43:11	2026-05-27 22:43:11
CR091	Peter	Wilson	14 Park Road	Ealing	London	W5 2AA	020-5555-2204	House	1600.00	Family with two children.	SL21 	B005	2026-05-27 22:43:11	2026-05-27 22:43:11
CR105	Janet	Mills	61 Redland Road	\N	Bristol	BS6 6QP	0117-555-2205	Flat	550.00	Looking for quiet area.	SB22 	B007	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: inspections; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.inspections (inspection_id, property_no, staff_no, inspection_date, comments, created_at, updated_at) FROM stdin;
1	PG004	SG022	2025-04-12	No problems found. Property in good condition.	2026-05-27 22:43:11	2026-05-27 22:43:11
2	PG004	SG022	2025-09-30	Cracked ceiling in living room. Requires urgent repair.	2026-05-27 22:43:11	2026-05-27 22:43:11
3	PG004	SG022	2026-01-15	Ceiling repair completed. Property back in good condition.	2026-05-27 22:43:11	2026-05-27 22:43:11
4	PG021	SG044	2025-07-01	Crockery needs to be replaced.	2026-05-27 22:43:11	2026-05-27 22:43:11
5	PG016	SG055	2025-06-15	Garden overgrown. Tenant reminded of maintenance duties.	2026-05-27 22:43:11	2026-05-27 22:43:11
6	PL001	SL063	2025-07-20	Property well maintained. No issues.	2026-05-27 22:43:11	2026-05-27 22:43:11
7	PL002	SL074	2025-08-05	Boiler making noise. Maintenance team notified.	2026-05-27 22:43:11	2026-05-27 22:43:11
8	PB002	SB055	2025-09-10	Damp patch found in bathroom ceiling. Needs inspection by plumber.	2026-05-27 22:43:11	2026-05-27 22:43:11
9	PB001	SB044	2026-02-11	Fence repaired and property clean.	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: leases; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.leases (lease_no, monthly_rent, payment_method, deposit, deposit_paid, date_start, date_end, duration_month, client_no, property_no, staff_no, created_at, updated_at) FROM stdin;
L0001	600.00	Direct Debit	600.00	t	2025-09-01	2026-08-31	12	CR074	PG021	SG37 	2026-05-27 22:43:11	2026-05-27 22:43:11
L0002	800.00	Standing Order	800.00	t	2025-12-01	2026-05-31	6	CR083	PL001	SL21 	2026-05-27 22:43:11	2026-05-27 22:43:11
L0003	480.00	Direct Debit	480.00	t	2025-10-01	2026-03-31	6	CR105	PB002	SB11 	2026-05-27 22:43:11	2026-05-27 22:43:11
L0004	375.00	Cash	375.00	f	2024-07-01	2025-06-30	12	CR062	PG036	SG37 	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: managers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.managers (staff_no, date_start, car_allowance, bonus, created_at, updated_at) FROM stdin;
SG37 	1990-04-01	1800.00	300.00	2026-05-27 22:43:11	2026-05-27 22:43:11
SL21 	1989-09-19	2000.00	340.00	2026-05-27 22:43:11	2026-05-27 22:43:11
SB11 	1993-01-01	1750.00	280.00	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_04_30_173332_create_branches_table	1
5	2026_04_30_173338_create_owners_table	1
6	2026_04_30_173345_create_properties_table	1
7	2026_05_04_153014_create_clients_table	1
8	2026_05_04_155032_create_staff_table	1
9	2026_05_06_155132_create_managers_table	1
10	2026_05_06_155142_create_secretaries_table	1
11	2026_05_06_155149_create_supervisors_table	1
12	2026_05_06_155155_create_next_of_kins_table	1
13	2026_05_06_162650_create_viewings_table	1
14	2026_05_06_162658_create_leases_table	1
15	2026_05_06_162704_create_inspections_table	1
16	2026_05_11_060926_create_personal_access_tokens_table	2
17	2026_05_26_000000_create_client_requests_table	3
18	2026_05_27_000000_add_request_and_viewing_indexes	4
19	2026_05_27_210000_add_case_study_accuracy_objects	5
20	2026_05_27_211000_add_case_study_retention_rules	6
21	2026_05_27_212000_add_staff_case_study_rules	7
\.


--
-- Data for Name: newspapers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.newspapers (id, name, street, city, postcode, tel_no, contact_name, created_at, updated_at) FROM stdin;
1	Glasgow Daily News	45 Argyle Street	Glasgow	G2 8AA	0141-555-3301	Fiona Kerr	2026-05-27 22:43:11	2026-05-27 22:43:11
2	London Evening Standard	10 Fleet Street	London	EC4Y 1AA	020-5555-3302	Martin Cole	2026-05-27 22:43:11	2026-05-27 22:43:11
3	Bristol Post	2 Temple Way	Bristol	BS1 6AA	0117-555-3303	Alice Morgan	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: next_of_kins; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.next_of_kins (staff_no, full_name, relationship, street, city, tel_no, created_at, updated_at) FROM stdin;
SG37 	Ann Beech Kin	Family	12 Wood Street	Glasgow	0141-555-0101	2026-05-27 22:43:11	2026-05-27 22:43:11
SG014	David Ford Kin	Family	4 Hill Road	Glasgow	0141-555-0102	2026-05-27 22:43:11	2026-05-27 22:43:11
SG005	Susan Brand Kin	Family	15 Queen Street	Glasgow	0141-555-0103	2026-05-27 22:43:11	2026-05-27 22:43:11
SG022	Robert Bell Kin	Family	20 Kelvin Road	Glasgow	0141-555-0104	2026-05-27 22:43:11	2026-05-27 22:43:11
SG033	Karen MacLeod Kin	Family	6 Clyde Avenue	Glasgow	0141-555-0105	2026-05-27 22:43:11	2026-05-27 22:43:11
SG044	Peter Ross Kin	Family	43 High Street	Glasgow	0141-555-0106	2026-05-27 22:43:11	2026-05-27 22:43:11
SG055	Mairi Campbell Kin	Family	9 River Walk	Glasgow	0141-555-0107	2026-05-27 22:43:11	2026-05-27 22:43:11
SL21 	John White Kin	Family	19 Taylor Street	London	0171-884-5112	2026-05-27 22:43:11	2026-05-27 22:43:11
SL41 	Julie Lee Kin	Family	11 Baker Street	London	020-5555-0201	2026-05-27 22:43:11	2026-05-27 22:43:11
SL52 	Carol Dean Kin	Family	87 King Road	London	020-5555-0202	2026-05-27 22:43:11	2026-05-27 22:43:11
SL063	Michael Grant Kin	Family	23 Oakfield Avenue	London	020-5555-0203	2026-05-27 22:43:11	2026-05-27 22:43:11
SL074	Rachel Stone Kin	Family	5 Park Lane	London	020-5555-0204	2026-05-27 22:43:11	2026-05-27 22:43:11
SL085	Andrew King Kin	Family	70 Victoria Road	London	020-5555-0205	2026-05-27 22:43:11	2026-05-27 22:43:11
SL096	Helen Clark Kin	Family	18 Grove Road	London	020-5555-0206	2026-05-27 22:43:11	2026-05-27 22:43:11
SB11 	George Russell Kin	Family	17 Bridge Street	Bristol	0117-555-0301	2026-05-27 22:43:11	2026-05-27 22:43:11
SB22 	Elaine Moore Kin	Family	42 Redland Road	Bristol	0117-555-0302	2026-05-27 22:43:11	2026-05-27 22:43:11
SB33 	Nina Patel Kin	Family	3 College Green	Bristol	0117-555-0303	2026-05-27 22:43:11	2026-05-27 22:43:11
SB044	Thomas Hill Kin	Family	6 Park Row	Bristol	0117-555-0304	2026-05-27 22:43:11	2026-05-27 22:43:11
SB055	Olivia Green Kin	Family	14 Queen Square	Bristol	0117-555-0305	2026-05-27 22:43:11	2026-05-27 22:43:11
SB066	Daniel Price Kin	Family	21 Harbour Road	Bristol	0117-555-0306	2026-05-27 22:43:11	2026-05-27 22:43:11
SB077	Emma Watson Kin	Family	10 Clifton Down	Bristol	0117-555-0307	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: owners; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.owners (owner_no, f_name, l_name, street, city, postcode, tel_no, created_at, updated_at) FROM stdin;
O0001	Tony	Shaw	1 West George Street	Glasgow	G2 1AA	0141-555-1101	2026-05-27 22:43:11	2026-05-27 22:43:11
O0002	Mary	Trevor	7 Rose Street	Glasgow	G3 6AB	0141-555-1102	2026-05-27 22:43:11	2026-05-27 22:43:11
O0003	Helen	Parker	44 Baker Street	London	NW1 6XE	020-5555-1103	2026-05-27 22:43:11	2026-05-27 22:43:11
O0004	James	Milton	9 Queen Street	London	W1 4AA	020-5555-1104	2026-05-27 22:43:11	2026-05-27 22:43:11
O0005	Sarah	Jones	31 Clifton Road	Bristol	BS8 1AA	0117-555-1105	2026-05-27 22:43:11	2026-05-27 22:43:11
O0006	William	Davies	66 Redland Grove	Bristol	BS6 6AB	0117-555-1106	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: properties; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.properties (property_no, street, area, city, postcode, type, rooms, rent, is_available, owner_no, branch_no, created_at, updated_at, staff_no) FROM stdin;
PG004	6 Lawrence Street	Partick	Glasgow	G11 9QX	Flat	3	350.00	t	O0001	B003	2026-05-27 22:43:11	2026-05-27 22:43:11	SG022
PG036	2 Manor Road	\N	Glasgow	G32 4QX	Flat	3	375.00	t	O0002	B003	2026-05-27 22:43:11	2026-05-27 22:43:11	SG033
PG021	18 Dale Road	Hyndland	Glasgow	G12	House	5	600.00	t	O0001	B003	2026-05-27 22:43:11	2026-05-27 22:43:11	SG044
PG016	5 Novar Drive	Hyndland	Glasgow	G12 9AX	Flat	4	450.00	t	O0002	B003	2026-05-27 22:43:11	2026-05-27 22:43:11	SG055
PL001	47 Oakfield Avenue	Ealing	London	W5 1AA	Flat	4	800.00	t	O0003	B005	2026-05-27 22:43:11	2026-05-27 22:43:11	SL063
PL002	12 Kensington Road	Kensington	London	W8 7AA	House	6	1500.00	t	O0004	B005	2026-05-27 22:43:11	2026-05-27 22:43:11	SL074
PL003	9 Baker Street	Marylebone	London	NW1 6XE	Flat	2	950.00	f	O0003	B005	2022-05-27 22:43:11	2022-05-27 22:43:11	SL085
PB001	22 Clifton Road	Clifton	Bristol	BS8 1AA	House	5	700.00	t	O0005	B007	2026-05-27 22:43:11	2026-05-27 22:43:11	SB044
PB002	5 Redland Grove	Redland	Bristol	BS6 6QP	Flat	3	480.00	t	O0006	B007	2026-05-27 22:43:11	2026-05-27 22:43:11	SB055
PB003	14 Harbour View	Harbourside	Bristol	BS1 6SX	Studio	1	325.00	t	O0006	B007	2026-05-27 22:43:11	2026-05-27 22:43:11	SB066
\.


--
-- Data for Name: property_audit_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.property_audit_log (log_id, property_no, old_status, new_status, changed_at, changed_by) FROM stdin;
\.


--
-- Data for Name: secretaries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.secretaries (staff_no, typing_speed, created_at, updated_at) FROM stdin;
SG005	62	2026-05-27 22:43:11	2026-05-27 22:43:11
SL52 	72	2026-05-27 22:43:11	2026-05-27 22:43:11
SB33 	68	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
hZAkqlUXZxiOW07gTt5EuLBCVb664BNDSKCwGQvv	2	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36	eyJfdG9rZW4iOiJVT1FSRmd2cWtrUXJRSzl0aER1ZUZ2MHJkZnhzY0pjTUxRT2k3b3FvIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvZHJlYW1ob21lLnRlc3RcL3Byb3BlcnRpZXMifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2RyZWFtaG9tZS50ZXN0XC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyfQ==	1779893409
\.


--
-- Data for Name: staff; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.staff (staff_no, f_name, l_name, street, area, city, postcode, tel_no, sex, dob, nin, "position", salary, date_joined, branch_no, supervisor_no, created_at, updated_at) FROM stdin;
SG37 	Ann	Beech	12 Wood Street	Kelvinside	Glasgow	G12 8QQ	0141-555-0101	F	1960-11-10	WL220658D	Manager	32000.00	1989-03-01	B003	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SG014	David	Ford	4 Hill Road	Partick	Glasgow	G11 7AB	0141-555-0102	M	1974-04-15	DF442011B	Supervisor	26000.00	1995-06-12	B003	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SG005	Susan	Brand	15 Queen Street	Partick	Glasgow	G11 5AD	0141-555-0103	F	1980-08-20	SB775533A	Secretary	19000.00	2001-09-03	B003	SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SG022	Robert	Bell	20 Kelvin Road	Hyndland	Glasgow	G12 9AA	0141-555-0104	M	1985-02-11	RB390112C	Staff	21000.00	2008-01-21	B003	SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SG033	Karen	MacLeod	6 Clyde Avenue	Patrick	Glasgow	G11 8AL	0141-555-0105	F	1987-07-23	KM778899D	Staff	21500.00	2010-05-10	B003	SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SG044	Peter	Ross	43 High Street	Dennistoun	Glasgow	G31 2TT	0141-555-0106	M	1990-09-12	PR220011E	Staff	20500.00	2016-03-14	B003	SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SG055	Mairi	Campbell	9 River Walk	Partick	Glasgow	G11 6ZZ	0141-555-0107	F	1992-12-05	MC998877F	Staff	20800.00	2018-07-02	B003	SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SL21 	John	White	19 Taylor Street	Cranford	London	SW1 4EH	0171-884-5112	M	1945-10-01	WK442011B	Manager	30000.00	1988-10-24	B005	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SL41 	Julie	Lee	11 Baker Street	Marylebone	London	NW1 6XE	020-5555-0201	F	1973-06-17	JL338899C	Supervisor	27000.00	1996-11-18	B005	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SL52 	Carol	Dean	87 King Road	Chelsea	London	SW3 5XP	020-5555-0202	F	1984-05-05	CD445566D	Secretary	19500.00	2004-02-09	B005	SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SL063	Michael	Grant	23 Oakfield Avenue	Ealing	London	W5 1AA	020-5555-0203	M	1986-01-19	MG112233E	Staff	22000.00	2011-08-22	B005	SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SL074	Rachel	Stone	5 Park Lane	Kensington	London	W8 7AA	020-5555-0204	F	1988-03-30	RS221144F	Staff	21800.00	2013-10-07	B005	SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SL085	Andrew	King	70 Victoria Road	Hammersmith	London	W6 9BB	020-5555-0205	M	1991-06-09	AK667788G	Staff	21400.00	2017-04-17	B005	SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SL096	Helen	Clark	18 Grove Road	Fulham	London	SW6 3DD	020-5555-0206	F	1993-09-18	HC112277H	Staff	21200.00	2019-06-24	B005	SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB11 	George	Russell	17 Bridge Street	Clifton	Bristol	BS8 2PQ	0117-555-0301	M	1968-12-02	GR991122A	Manager	30500.00	1992-04-13	B007	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SB22 	Elaine	Moore	42 Redland Road	Redland	Bristol	BS6 6QP	0117-555-0302	F	1978-10-12	EM443322B	Supervisor	25500.00	2000-09-11	B007	\N	2026-05-27 22:43:11	2026-05-27 22:43:11
SB33 	Nina	Patel	3 College Green	Clifton	Bristol	BS1 5TR	0117-555-0303	F	1986-07-27	NP667700C	Secretary	18800.00	2006-01-16	B007	SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB044	Thomas	Hill	6 Park Row	Clifton	Bristol	BS1 5LJ	0117-555-0304	M	1989-02-14	TH118899D	Staff	20700.00	2014-03-19	B007	SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB055	Olivia	Green	14 Queen Square	Central	Bristol	BS1 4NT	0117-555-0305	F	1990-05-24	OG224466E	Staff	20900.00	2015-05-05	B007	SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB066	Daniel	Price	21 Harbour Road	Harbourside	Bristol	BS1 6SX	0117-555-0306	M	1992-11-03	DP779911F	Staff	20600.00	2018-02-12	B007	SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB077	Emma	Watson	10 Clifton Down	Clifton	Bristol	BS8 3HT	0117-555-0307	F	1994-04-28	EW335577G	Staff	20400.00	2020-10-01	B007	SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: supervisors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.supervisors (staff_no, created_at, updated_at) FROM stdin;
SG014	2026-05-27 22:43:11	2026-05-27 22:43:11
SL41 	2026-05-27 22:43:11	2026-05-27 22:43:11
SB22 	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role) FROM stdin;
1	Admin User	admin@dreamhome.com	\N	$2y$10$6ndauI7QQG/Aj6yqLLKzlu.uTR9nUYSM2BVWFWxD2MKju/wG/WmmG	\N	2026-05-27 22:43:11	2026-05-27 22:43:11	admin
3	Supervisor User	supervisor@dreamhome.com	\N	$2y$10$6ndauI7QQG/Aj6yqLLKzlu.uTR9nUYSM2BVWFWxD2MKju/wG/WmmG	\N	2026-05-27 22:43:11	2026-05-27 22:43:11	supervisor
4	Staff User	staff@dreamhome.com	\N	$2y$10$6ndauI7QQG/Aj6yqLLKzlu.uTR9nUYSM2BVWFWxD2MKju/wG/WmmG	\N	2026-05-27 22:43:11	2026-05-27 22:43:11	staff
2	Manager User	manager@dreamhome.com	\N	$2y$12$k.qZ38xU.wc4VhebdtdiZOD2PB8aJvW46vvXX3lF1hX1RO1cxlgT.	\N	2026-05-27 22:43:11	2026-05-27 14:45:02	manager
\.


--
-- Data for Name: viewings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.viewings (client_no, property_no, view_date, staff_no, comments, created_at, updated_at) FROM stdin;
CR074	PG021	2025-07-01	SG044	Crockery needs to be replaced.	2026-05-27 22:43:11	2026-05-27 22:43:11
CR056	PG004	2025-04-12	SG022	No problems found. Property in good condition.	2026-05-27 22:43:11	2026-05-27 22:43:11
CR062	PG036	2025-05-10	SG033	Minor scuff marks on walls. Otherwise clean.	2026-05-27 22:43:11	2026-05-27 22:43:11
CR083	PL001	2025-08-05	SL063	Client liked location and asked about parking.	2026-05-27 22:43:11	2026-05-27 22:43:11
CR091	PL002	2025-08-18	SL074	Suitable for family, requested lease terms.	2026-05-27 22:43:11	2026-05-27 22:43:11
CR105	PB002	2025-09-10	SB055	Damp patch found in bathroom ceiling.	2026-05-27 22:43:11	2026-05-27 22:43:11
\.


--
-- Name: adverts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.adverts_id_seq', 5, true);


--
-- Name: client_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.client_requests_id_seq', 1, true);


--
-- Name: inspections_inspection_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.inspections_inspection_id_seq', 9, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 21, true);


--
-- Name: newspapers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.newspapers_id_seq', 3, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: property_audit_log_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.property_audit_log_log_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- Name: adverts adverts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adverts
    ADD CONSTRAINT adverts_pkey PRIMARY KEY (id);


--
-- Name: branches branches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.branches
    ADD CONSTRAINT branches_pkey PRIMARY KEY (branch_no);


--
-- Name: client_requests client_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client_requests
    ADD CONSTRAINT client_requests_pkey PRIMARY KEY (id);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (client_no);


--
-- Name: inspections inspections_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inspections
    ADD CONSTRAINT inspections_pkey PRIMARY KEY (inspection_id);


--
-- Name: leases leases_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.leases
    ADD CONSTRAINT leases_pkey PRIMARY KEY (lease_no);


--
-- Name: managers managers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.managers
    ADD CONSTRAINT managers_pkey PRIMARY KEY (staff_no);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: newspapers newspapers_name_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.newspapers
    ADD CONSTRAINT newspapers_name_unique UNIQUE (name);


--
-- Name: newspapers newspapers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.newspapers
    ADD CONSTRAINT newspapers_pkey PRIMARY KEY (id);


--
-- Name: next_of_kins next_of_kins_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.next_of_kins
    ADD CONSTRAINT next_of_kins_pkey PRIMARY KEY (staff_no);


--
-- Name: owners owners_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.owners
    ADD CONSTRAINT owners_pkey PRIMARY KEY (owner_no);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: properties properties_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.properties
    ADD CONSTRAINT properties_pkey PRIMARY KEY (property_no);


--
-- Name: property_audit_log property_audit_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.property_audit_log
    ADD CONSTRAINT property_audit_log_pkey PRIMARY KEY (log_id);


--
-- Name: secretaries secretaries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secretaries
    ADD CONSTRAINT secretaries_pkey PRIMARY KEY (staff_no);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: staff staff_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff
    ADD CONSTRAINT staff_pkey PRIMARY KEY (staff_no);


--
-- Name: supervisors supervisors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supervisors
    ADD CONSTRAINT supervisors_pkey PRIMARY KEY (staff_no);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: viewings viewings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viewings
    ADD CONSTRAINT viewings_pkey PRIMARY KEY (client_no, property_no, view_date);


--
-- Name: idx_adverts_newspaper; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_adverts_newspaper ON public.adverts USING btree (newspaper_id);


--
-- Name: idx_adverts_property; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_adverts_property ON public.adverts USING btree (property_no);


--
-- Name: idx_client_requests_property; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_client_requests_property ON public.client_requests USING btree (property_no);


--
-- Name: idx_client_requests_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_client_requests_status ON public.client_requests USING btree (status);


--
-- Name: idx_inspections_property; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_inspections_property ON public.inspections USING btree (property_no);


--
-- Name: idx_leases_client; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_leases_client ON public.leases USING btree (client_no);


--
-- Name: idx_leases_property; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_leases_property ON public.leases USING btree (property_no);


--
-- Name: idx_properties_available; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_properties_available ON public.properties USING btree (is_available);


--
-- Name: idx_properties_branch; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_properties_branch ON public.properties USING btree (branch_no);


--
-- Name: idx_properties_rent; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_properties_rent ON public.properties USING btree (rent);


--
-- Name: idx_properties_staff; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_properties_staff ON public.properties USING btree (staff_no);


--
-- Name: idx_properties_type; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_properties_type ON public.properties USING btree (type);


--
-- Name: idx_staff_branch; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_staff_branch ON public.staff USING btree (branch_no);


--
-- Name: idx_staff_position; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_staff_position ON public.staff USING btree ("position");


--
-- Name: idx_viewings_property; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_viewings_property ON public.viewings USING btree (property_no);


--
-- Name: idx_viewings_staff; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_viewings_staff ON public.viewings USING btree (staff_no);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: uniq_branch_manager; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_branch_manager ON public.staff USING btree (branch_no) WHERE ((("position")::text = 'Manager'::text) AND (branch_no IS NOT NULL));


--
-- Name: leases trg_lease_dates; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_lease_dates BEFORE INSERT OR UPDATE OF date_start, date_end, duration_month ON public.leases FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_lease_dates();


--
-- Name: leases trg_lease_retention; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_lease_retention BEFORE DELETE ON public.leases FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_lease_retention();


--
-- Name: properties trg_minimum_rent; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_minimum_rent BEFORE INSERT OR UPDATE ON public.properties FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_minimum_rent();


--
-- Name: leases trg_prevent_active_lease_delete; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_prevent_active_lease_delete BEFORE DELETE ON public.leases FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_prevent_lease_delete();


--
-- Name: properties trg_property_retention; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_property_retention BEFORE DELETE ON public.properties FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_property_retention();


--
-- Name: properties trg_property_status_change; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_property_status_change AFTER UPDATE ON public.properties FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_property_status();


--
-- Name: properties trg_staff_property_limit; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_staff_property_limit BEFORE INSERT OR UPDATE OF staff_no ON public.properties FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_staff_property_limit();


--
-- Name: staff trg_staff_updated_at; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_staff_updated_at BEFORE UPDATE ON public.staff FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_staff_updated_at();


--
-- Name: staff trg_supervisor_rules; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_supervisor_rules BEFORE INSERT OR UPDATE OF supervisor_no ON public.staff FOR EACH ROW EXECUTE FUNCTION public.fn_trigger_supervisor_rules();


--
-- Name: adverts adverts_newspaper_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adverts
    ADD CONSTRAINT adverts_newspaper_id_foreign FOREIGN KEY (newspaper_id) REFERENCES public.newspapers(id) ON DELETE CASCADE;


--
-- Name: adverts adverts_property_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.adverts
    ADD CONSTRAINT adverts_property_no_foreign FOREIGN KEY (property_no) REFERENCES public.properties(property_no) ON DELETE CASCADE;


--
-- Name: client_requests client_requests_approved_client_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client_requests
    ADD CONSTRAINT client_requests_approved_client_no_foreign FOREIGN KEY (approved_client_no) REFERENCES public.clients(client_no) ON DELETE SET NULL;


--
-- Name: client_requests client_requests_property_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.client_requests
    ADD CONSTRAINT client_requests_property_no_foreign FOREIGN KEY (property_no) REFERENCES public.properties(property_no) ON DELETE SET NULL;


--
-- Name: clients clients_branch_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_branch_no_foreign FOREIGN KEY (branch_no) REFERENCES public.branches(branch_no);


--
-- Name: inspections inspections_property_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inspections
    ADD CONSTRAINT inspections_property_no_foreign FOREIGN KEY (property_no) REFERENCES public.properties(property_no) ON DELETE SET NULL;


--
-- Name: inspections inspections_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.inspections
    ADD CONSTRAINT inspections_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE SET NULL;


--
-- Name: leases leases_client_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.leases
    ADD CONSTRAINT leases_client_no_foreign FOREIGN KEY (client_no) REFERENCES public.clients(client_no) ON DELETE SET NULL;


--
-- Name: leases leases_property_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.leases
    ADD CONSTRAINT leases_property_no_foreign FOREIGN KEY (property_no) REFERENCES public.properties(property_no) ON DELETE SET NULL;


--
-- Name: leases leases_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.leases
    ADD CONSTRAINT leases_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE SET NULL;


--
-- Name: managers managers_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.managers
    ADD CONSTRAINT managers_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE CASCADE;


--
-- Name: next_of_kins next_of_kins_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.next_of_kins
    ADD CONSTRAINT next_of_kins_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE CASCADE;


--
-- Name: properties properties_branch_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.properties
    ADD CONSTRAINT properties_branch_no_foreign FOREIGN KEY (branch_no) REFERENCES public.branches(branch_no) ON DELETE SET NULL;


--
-- Name: properties properties_owner_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.properties
    ADD CONSTRAINT properties_owner_no_foreign FOREIGN KEY (owner_no) REFERENCES public.owners(owner_no) ON DELETE SET NULL;


--
-- Name: properties properties_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.properties
    ADD CONSTRAINT properties_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE SET NULL;


--
-- Name: secretaries secretaries_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.secretaries
    ADD CONSTRAINT secretaries_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE CASCADE;


--
-- Name: staff staff_branch_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff
    ADD CONSTRAINT staff_branch_no_foreign FOREIGN KEY (branch_no) REFERENCES public.branches(branch_no) ON DELETE SET NULL;


--
-- Name: staff staff_supervisor_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.staff
    ADD CONSTRAINT staff_supervisor_no_foreign FOREIGN KEY (supervisor_no) REFERENCES public.staff(staff_no) ON DELETE SET NULL;


--
-- Name: supervisors supervisors_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.supervisors
    ADD CONSTRAINT supervisors_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE CASCADE;


--
-- Name: viewings viewings_client_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viewings
    ADD CONSTRAINT viewings_client_no_foreign FOREIGN KEY (client_no) REFERENCES public.clients(client_no) ON DELETE CASCADE;


--
-- Name: viewings viewings_property_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viewings
    ADD CONSTRAINT viewings_property_no_foreign FOREIGN KEY (property_no) REFERENCES public.properties(property_no) ON DELETE CASCADE;


--
-- Name: viewings viewings_staff_no_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.viewings
    ADD CONSTRAINT viewings_staff_no_foreign FOREIGN KEY (staff_no) REFERENCES public.staff(staff_no) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict NfjHzF1jDxe6JSBAnE4YxZ6SlWTWOgDVEfQbc8Txefn9UwfPCnKT197X92R4Gbg

