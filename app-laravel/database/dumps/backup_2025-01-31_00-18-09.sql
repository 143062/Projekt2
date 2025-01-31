--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2 (Debian 17.2-1.pgdg120+1)
-- Dumped by pg_dump version 17.2

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO "user";

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO "user";

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO "user";

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO "user";

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: friends; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.friends (
    user_id uuid NOT NULL,
    friend_id uuid NOT NULL
);


ALTER TABLE public.friends OWNER TO "user";

--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO "user";

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO "user";

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO "user";

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO "user";

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO "user";

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notes; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.notes (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notes OWNER TO "user";

--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id uuid NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO "user";

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: user
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO "user";

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: user
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.roles (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.roles OWNER TO "user";

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO "user";

--
-- Name: shared_notes; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.shared_notes (
    note_id uuid NOT NULL,
    user_id uuid NOT NULL
);


ALTER TABLE public.shared_notes OWNER TO "user";

--
-- Name: users; Type: TABLE; Schema: public; Owner: user
--

CREATE TABLE public.users (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    login character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    profile_picture character varying(255) DEFAULT 'public/img/profile/default/default_profile_picture.jpg'::character varying NOT NULL,
    role_id uuid,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO "user";

--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: friends; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.friends (user_id, friend_id) FROM stdin;
27ab346c-c5aa-4c69-87e3-70b1cc9914c8	29584de3-22cf-45c9-be1f-c9710300cbe4
ab3e524c-c7d4-41de-8f30-7e6d3ead0034	27ab346c-c5aa-4c69-87e3-70b1cc9914c8
27ab346c-c5aa-4c69-87e3-70b1cc9914c8	ab3e524c-c7d4-41de-8f30-7e6d3ead0034
29584de3-22cf-45c9-be1f-c9710300cbe4	27ab346c-c5aa-4c69-87e3-70b1cc9914c8
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.migrations (id, migration, batch) FROM stdin;
19	0001_01_01_000001_create_cache_table	1
20	0001_01_01_000002_create_jobs_table	1
21	2025_01_01_000001_create_roles_table	1
22	2025_01_01_000002_create_users_table	1
23	2025_01_01_000003_create_notes_table	1
24	2025_01_01_000004_create_friends_table	1
25	2025_01_01_000005_create_shared_notes_table	1
26	2025_01_27_161947_create_personal_access_tokens_table	1
27	2025_01_27_170338_create_sessions_table	1
\.


--
-- Data for Name: notes; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.notes (id, user_id, title, content, created_at, updated_at) FROM stdin;
9648389e-65cb-4aea-bf9a-2ddb9d986493	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	moja kolejna notatka	to jest ona	2025-01-30 00:13:15	2025-01-30 00:13:15
c46e1bcd-84a7-4f50-9928-25288a0999ad	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	trzecia notatka	teraz	2025-01-30 00:14:18	2025-01-30 00:14:18
a2b5874b-e6a4-41bd-9eb4-d504b91c61a0	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	notatka ktora probuje udostepnic komus	tresc tej notatki	2025-01-30 02:41:52	2025-01-30 02:41:52
8e79d8c0-e28b-4827-bb28-7d29acb35d8b	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	teraz próbuje wyslac notatke	wybralem, ze udostepnie ja uzytkownikowi test6test6	2025-01-30 02:58:09	2025-01-30 02:58:09
618d9e71-b055-43ca-bc8a-cf497d507ceb	29584de3-22cf-45c9-be1f-c9710300cbe4	notatka dla testowicza	no siema, dzialasz tam?	2025-01-30 03:14:55	2025-01-30 03:14:55
f76a17cd-4a54-4a7d-9249-b02d230c1f53	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	jheszcze jedna	takj	2025-01-30 03:23:11	2025-01-30 03:23:11
f77c071c-1251-4417-ae69-ae99a0c6b4de	29584de3-22cf-45c9-be1f-c9710300cbe4	druga notatka dla testowicza	dzialaj	2025-01-30 03:35:44	2025-01-30 03:35:44
6cd9bc9a-3603-4edd-bf41-21c3989a9c90	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	Moja pierwsza notatka	Treść mojej pierwszej notatki tak jest xd	2025-01-29 09:52:31	2025-01-30 04:46:37
99087356-cd3d-4cc1-bd38-a380d4ca254e	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	nowa notatka dla calej 2	tak	2025-01-30 04:58:13	2025-01-31 00:14:50
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
1	App\\Models\\User	962f07e2-596c-4bb4-b3d6-cc43a2e3697e	auth_token	19b77612a57cb0c5f9c2132bf913df94e72511c365529d4cd3d4841dcc154a04	["*"]	\N	\N	2025-01-28 22:38:00	2025-01-28 22:38:00
2	App\\Models\\User	2ed0a0f6-f480-4cec-80f4-b651bbfbd421	auth_token	00893ba6b30542b01f5cbec6d1b61261f5e37f5a31eeeac31583fafc014e59ae	["*"]	\N	\N	2025-01-28 23:24:39	2025-01-28 23:24:39
3	App\\Models\\User	a5599252-8de8-47d2-a48f-d1195aa2a3c1	auth_token	0e5abb1b4e9c398c18c1f7935b16af530955463dc32d110fc06ff09edbc990f1	["*"]	\N	\N	2025-01-28 23:39:29	2025-01-28 23:39:29
4	App\\Models\\User	cedb9dfa-cfe2-41a1-b2ef-d7c2536c32bb	auth_token	54f5972033c90665e6cbb69b81c4991d93060980dce1efc4ea387fdfcb773157	["*"]	\N	\N	2025-01-28 23:55:59	2025-01-28 23:55:59
5	App\\Models\\User	43446d55-036f-44f0-bb5c-ed4183f113a1	auth_token	0a0c5390a8da663097d84de0d9059b909cf32cf897443cf100d6848ba9cb9c32	["*"]	\N	\N	2025-01-29 00:16:49	2025-01-29 00:16:49
6	App\\Models\\User	43446d55-036f-44f0-bb5c-ed4183f113a1	auth_token	82d76e714a1d9f8eef4625a0ae4a5f35e3d3e6066a88026d5c1a90642e3e94fb	["*"]	\N	\N	2025-01-29 00:17:14	2025-01-29 00:17:14
34	App\\Models\\User	43446d55-036f-44f0-bb5c-ed4183f113a1	auth_token	d965f49a728ac8710da4b5d061a399176f6b974db046fbeca7044b7c514416cb	["*"]	2025-01-30 03:31:07	\N	2025-01-30 03:30:26	2025-01-30 03:31:07
20	App\\Models\\User	43446d55-036f-44f0-bb5c-ed4183f113a1	auth_token	9b483a70df06b8031c3a575e5685cece53ac264ffee4ac2ff96813a853c038de	["*"]	2025-01-29 23:34:42	\N	2025-01-29 23:34:28	2025-01-29 23:34:42
75	App\\Models\\User	29584de3-22cf-45c9-be1f-c9710300cbe4	auth_token	e72a83777bfb35471560eb48c055c516bd6ecd44b8022a93266c18f2dd1b1813	["*"]	2025-01-31 00:16:49	\N	2025-01-31 00:16:27	2025-01-31 00:16:49
36	App\\Models\\User	43446d55-036f-44f0-bb5c-ed4183f113a1	auth_token	4a5c3c89bde852bf31aef3dbd7da935f40374e1cf02673537b175d97fce67a9c	["*"]	2025-01-30 03:34:28	\N	2025-01-30 03:33:49	2025-01-30 03:34:28
76	App\\Models\\User	b74799b0-a57a-4057-aba8-09c419c5cf57	auth_token	84d0377b78106ab00c0e36f1428adb91a5e611b1e77916fdbaf916305ec57d7d	["*"]	2025-01-31 00:18:09	\N	2025-01-31 00:17:18	2025-01-31 00:18:09
72	App\\Models\\User	27ab346c-c5aa-4c69-87e3-70b1cc9914c8	auth_token	dc485a41322de40a4664bf1736ed74a6b686214d826988839f9dcc9c2f5c71b8	["*"]	2025-01-31 00:15:03	\N	2025-01-31 00:13:50	2025-01-31 00:15:03
68	App\\Models\\User	435f8f5c-5584-47f4-979f-144d6fd23614	auth_token	5231d4fb2d4a40a8a0452e36e0e8142fabd9a1a68b77dd61a94f122ec42ca4e1	["*"]	2025-01-30 19:15:42	\N	2025-01-30 19:15:06	2025-01-30 19:15:42
74	App\\Models\\User	ab3e524c-c7d4-41de-8f30-7e6d3ead0034	auth_token	c6c1291a6e7b34657ace58698d0c9a7b85eb57073d2fca9960e67e19dd8d3ab5	["*"]	2025-01-31 00:16:11	\N	2025-01-31 00:15:56	2025-01-31 00:16:11
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.roles (id, name) FROM stdin;
713cfd86-8d26-45c3-a84a-42c5d5aebdf0	user
0c60e265-2004-4dae-8312-0facac127e18	admin
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
E3TAr38YUuO5POLPjQwpszQHwcAHsNLey8jQRDEh	\N	172.18.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36	YTozOntzOjY6Il90b2tlbiI7czo0MDoiTUx3aG1lNzB0dURQbzREOVk2VExkWEJ1c3VNUkwwY3ZkcmVnOXY3VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODA4MC9hZG1pbl9wYW5lbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1738282641
\.


--
-- Data for Name: shared_notes; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.shared_notes (note_id, user_id) FROM stdin;
8e79d8c0-e28b-4827-bb28-7d29acb35d8b	29584de3-22cf-45c9-be1f-c9710300cbe4
618d9e71-b055-43ca-bc8a-cf497d507ceb	27ab346c-c5aa-4c69-87e3-70b1cc9914c8
f77c071c-1251-4417-ae69-ae99a0c6b4de	27ab346c-c5aa-4c69-87e3-70b1cc9914c8
a2b5874b-e6a4-41bd-9eb4-d504b91c61a0	ab3e524c-c7d4-41de-8f30-7e6d3ead0034
6cd9bc9a-3603-4edd-bf41-21c3989a9c90	29584de3-22cf-45c9-be1f-c9710300cbe4
c46e1bcd-84a7-4f50-9928-25288a0999ad	29584de3-22cf-45c9-be1f-c9710300cbe4
c46e1bcd-84a7-4f50-9928-25288a0999ad	ab3e524c-c7d4-41de-8f30-7e6d3ead0034
99087356-cd3d-4cc1-bd38-a380d4ca254e	29584de3-22cf-45c9-be1f-c9710300cbe4
99087356-cd3d-4cc1-bd38-a380d4ca254e	ab3e524c-c7d4-41de-8f30-7e6d3ead0034
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: user
--

COPY public.users (id, login, email, password, profile_picture, role_id, created_at, updated_at) FROM stdin;
29584de3-22cf-45c9-be1f-c9710300cbe4	test6test6	test6test6@test.com	$2y$12$ILkV7HJisR./P/sSaQ/5HO3IRLLL6JVcwDOJObXrhmWT4FPfGCzbG	img/profile/29584de3-22cf-45c9-be1f-c9710300cbe4/profile.jpg	713cfd86-8d26-45c3-a84a-42c5d5aebdf0	2025-01-29 00:23:04	2025-01-29 23:32:18
27ab346c-c5aa-4c69-87e3-70b1cc9914c8	testowicz	testowicz@test.com	$2y$12$BxlDVPmFK198CAgzW6AE3e8imUFysC.ncazpCF4.NYSs64rCgNbDi	img/profile/27ab346c-c5aa-4c69-87e3-70b1cc9914c8/profile.jpg	713cfd86-8d26-45c3-a84a-42c5d5aebdf0	2025-01-29 01:32:17	2025-01-29 23:33:23
ab3e524c-c7d4-41de-8f30-7e6d3ead0034	test7test7	test7test7@test.com	$2y$12$OahsMQyWOSasslscL2/v7OWz5Giy0Y6x66FhGU1xHPwlwq46RXbti	img/profile/ab3e524c-c7d4-41de-8f30-7e6d3ead0034/profile.jpg	713cfd86-8d26-45c3-a84a-42c5d5aebdf0	2025-01-29 23:35:15	2025-01-29 23:46:36
b74799b0-a57a-4057-aba8-09c419c5cf57	admin	admin@admin.com	$2y$12$XmeLwNPf1xY8ljTNl.9VM.USd.XHW1GpycTbwO9kV61kd8ynKrcHK	img/profile/default/default_profile_picture.jpg	0c60e265-2004-4dae-8312-0facac127e18	2025-01-30 06:01:16	\N
71383b2c-7493-4bf4-ad31-10919d6b09ab	panelowicz	panelowicz@mail.com	$2y$12$dZ1wg6CrHAruzK/rypJJAumRz9vguL2LAGTPBpOkCNfX/Y47wdw3i	public/img/profile/default/default_profile_picture.jpg	713cfd86-8d26-45c3-a84a-42c5d5aebdf0	2025-01-30 20:13:51	2025-01-30 20:13:51
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.migrations_id_seq', 27, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: user
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 76, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: friends friends_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_pkey PRIMARY KEY (user_id, friend_id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notes notes_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: shared_notes shared_notes_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_pkey PRIMARY KEY (note_id, user_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_login_unique; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_login_unique UNIQUE (login);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: user
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: user
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: user
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: user
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: friends friends_friend_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_friend_id_foreign FOREIGN KEY (friend_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: friends friends_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: notes notes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: shared_notes shared_notes_note_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_note_id_foreign FOREIGN KEY (note_id) REFERENCES public.notes(id) ON DELETE CASCADE;


--
-- Name: shared_notes shared_notes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

