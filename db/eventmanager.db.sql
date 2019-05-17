--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.5
-- Dumped by pg_dump version 11.2

-- Started on 2019-05-17 12:34:48 EEST

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2280 (class 1262 OID 33672)
-- Name: eventmanager; Type: DATABASE; Schema: -; Owner: -
--

CREATE DATABASE eventmanager WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'en_US.UTF-8' LC_CTYPE = 'en_US.UTF-8';


\connect eventmanager

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 8 (class 2615 OID 33673)
-- Name: events; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA events;


SET default_with_oids = false;

--
-- TOC entry 174 (class 1259 OID 33674)
-- Name: events; Type: TABLE; Schema: events; Owner: -
--

CREATE TABLE events.events (
    start_date date,
    end_date date,
    max_slots integer,
    available_slots integer,
    id integer NOT NULL
);


--
-- TOC entry 176 (class 1259 OID 33686)
-- Name: events_id_seq; Type: SEQUENCE; Schema: events; Owner: -
--

CREATE SEQUENCE events.events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 176
-- Name: events_id_seq; Type: SEQUENCE OWNED BY; Schema: events; Owner: -
--

ALTER SEQUENCE events.events_id_seq OWNED BY events.events.id;


--
-- TOC entry 175 (class 1259 OID 33679)
-- Name: registrations; Type: TABLE; Schema: events; Owner: -
--

CREATE TABLE events.registrations (
    event_id integer,
    first_name character varying(250),
    last_name character varying(250),
    email character varying(250),
    phone character varying(20),
    id integer NOT NULL
);


--
-- TOC entry 177 (class 1259 OID 33694)
-- Name: registrations_id_seq; Type: SEQUENCE; Schema: events; Owner: -
--

CREATE SEQUENCE events.registrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 177
-- Name: registrations_id_seq; Type: SEQUENCE OWNED BY; Schema: events; Owner: -
--

ALTER SEQUENCE events.registrations_id_seq OWNED BY events.registrations.id;


--
-- TOC entry 2155 (class 2604 OID 33688)
-- Name: events id; Type: DEFAULT; Schema: events; Owner: -
--

ALTER TABLE ONLY events.events ALTER COLUMN id SET DEFAULT nextval('events.events_id_seq'::regclass);


--
-- TOC entry 2156 (class 2604 OID 33696)
-- Name: registrations id; Type: DEFAULT; Schema: events; Owner: -
--

ALTER TABLE ONLY events.registrations ALTER COLUMN id SET DEFAULT nextval('events.registrations_id_seq'::regclass);


--
-- TOC entry 2271 (class 0 OID 33674)
-- Dependencies: 174
-- Data for Name: events; Type: TABLE DATA; Schema: events; Owner: -
--

INSERT INTO events.events (start_date, end_date, max_slots, available_slots, id) VALUES ('2019-05-11', '2019-05-31', 15, 15, 1);


--
-- TOC entry 2272 (class 0 OID 33679)
-- Dependencies: 175
-- Data for Name: registrations; Type: TABLE DATA; Schema: events; Owner: -
--

INSERT INTO events.registrations (event_id, first_name, last_name, email, phone, id) VALUES (1, 'Nicolae', 'Tusinean', 'first.last@email.com', '+00555555555', 8);


--
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 176
-- Name: events_id_seq; Type: SEQUENCE SET; Schema: events; Owner: -
--

SELECT pg_catalog.setval('events.events_id_seq', 10, true);


--
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 177
-- Name: registrations_id_seq; Type: SEQUENCE SET; Schema: events; Owner: -
--

SELECT pg_catalog.setval('events.registrations_id_seq', 8, true);


--
-- TOC entry 2158 (class 2606 OID 33693)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: events; Owner: -
--

ALTER TABLE ONLY events.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- TOC entry 2160 (class 2606 OID 33701)
-- Name: registrations registrations_pkey; Type: CONSTRAINT; Schema: events; Owner: -
--

ALTER TABLE ONLY events.registrations
    ADD CONSTRAINT registrations_pkey PRIMARY KEY (id);


--
-- TOC entry 2161 (class 2606 OID 33738)
-- Name: registrations events_fk; Type: FK CONSTRAINT; Schema: events; Owner: -
--

ALTER TABLE ONLY events.registrations
    ADD CONSTRAINT events_fk FOREIGN KEY (event_id) REFERENCES events.events(id) ON DELETE CASCADE;


-- Completed on 2019-05-17 12:34:48 EEST

--
-- PostgreSQL database dump complete
--

