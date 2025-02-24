PGDMP      )                 }         
   notatki_db    17.2 (Debian 17.2-1.pgdg120+1)    17.2 C    �           0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false            �           0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false            �           0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false            �           1262    16384 
   notatki_db    DATABASE     u   CREATE DATABASE notatki_db WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';
    DROP DATABASE notatki_db;
                     user    false            �            1259    16423    cache    TABLE     �   CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);
    DROP TABLE public.cache;
       public         heap r       user    false            �            1259    16430    cache_locks    TABLE     �   CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);
    DROP TABLE public.cache_locks;
       public         heap r       user    false            �            1259    16455    failed_jobs    TABLE     &  CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);
    DROP TABLE public.failed_jobs;
       public         heap r       user    false            �            1259    16454    failed_jobs_id_seq    SEQUENCE     {   CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 )   DROP SEQUENCE public.failed_jobs_id_seq;
       public               user    false    227            �           0    0    failed_jobs_id_seq    SEQUENCE OWNED BY     I   ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;
          public               user    false    226            �            1259    16680    friends    TABLE     X   CREATE TABLE public.friends (
    user_id uuid NOT NULL,
    friend_id uuid NOT NULL
);
    DROP TABLE public.friends;
       public         heap r       user    false            �            1259    16447    job_batches    TABLE     d  CREATE TABLE public.job_batches (
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
    DROP TABLE public.job_batches;
       public         heap r       user    false            �            1259    16438    jobs    TABLE     �   CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);
    DROP TABLE public.jobs;
       public         heap r       user    false            �            1259    16437    jobs_id_seq    SEQUENCE     t   CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.jobs_id_seq;
       public               user    false    224            �           0    0    jobs_id_seq    SEQUENCE OWNED BY     ;   ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;
          public               user    false    223            �            1259    16390 
   migrations    TABLE     �   CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);
    DROP TABLE public.migrations;
       public         heap r       user    false            �            1259    16389    migrations_id_seq    SEQUENCE     �   CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE public.migrations_id_seq;
       public               user    false    218            �           0    0    migrations_id_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;
          public               user    false    217            �            1259    16683    notes    TABLE     �   CREATE TABLE public.notes (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    user_id uuid,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.notes;
       public         heap r       user    false            �            1259    16407    password_reset_tokens    TABLE     �   CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);
 )   DROP TABLE public.password_reset_tokens;
       public         heap r       user    false            �            1259    16690    roles    TABLE     x   CREATE TABLE public.roles (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    name character varying(100) NOT NULL
);
    DROP TABLE public.roles;
       public         heap r       user    false            �            1259    16414    sessions    TABLE     �   CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);
    DROP TABLE public.sessions;
       public         heap r       user    false            �            1259    16694    shared_notes    TABLE     �   CREATE TABLE public.shared_notes (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    note_id uuid,
    shared_with_user_id uuid,
    shared_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
     DROP TABLE public.shared_notes;
       public         heap r       user    false            �            1259    16699    users    TABLE     �  CREATE TABLE public.users (
    id uuid DEFAULT gen_random_uuid() NOT NULL,
    login character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    role_id uuid,
    profile_picture character varying(255) DEFAULT 'public/img/profile/default/default_profile_picture.jpg'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);
    DROP TABLE public.users;
       public         heap r       user    false            �           2604    16458    failed_jobs id    DEFAULT     p   ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);
 =   ALTER TABLE public.failed_jobs ALTER COLUMN id DROP DEFAULT;
       public               user    false    227    226    227            �           2604    16441    jobs id    DEFAULT     b   ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);
 6   ALTER TABLE public.jobs ALTER COLUMN id DROP DEFAULT;
       public               user    false    224    223    224            �           2604    16393    migrations id    DEFAULT     n   ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);
 <   ALTER TABLE public.migrations ALTER COLUMN id DROP DEFAULT;
       public               user    false    217    218    218            �          0    16423    cache 
   TABLE DATA           7   COPY public.cache (key, value, expiration) FROM stdin;
    public               user    false    221   �N       �          0    16430    cache_locks 
   TABLE DATA           =   COPY public.cache_locks (key, owner, expiration) FROM stdin;
    public               user    false    222   �N       �          0    16455    failed_jobs 
   TABLE DATA           a   COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
    public               user    false    227   �N       �          0    16680    friends 
   TABLE DATA           5   COPY public.friends (user_id, friend_id) FROM stdin;
    public               user    false    228   O       �          0    16447    job_batches 
   TABLE DATA           �   COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
    public               user    false    225   �O       �          0    16438    jobs 
   TABLE DATA           c   COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
    public               user    false    224   �O       �          0    16390 
   migrations 
   TABLE DATA           :   COPY public.migrations (id, migration, batch) FROM stdin;
    public               user    false    218   P       �          0    16683    notes 
   TABLE DATA           H   COPY public.notes (id, user_id, title, content, created_at) FROM stdin;
    public               user    false    229   [P       �          0    16407    password_reset_tokens 
   TABLE DATA           I   COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
    public               user    false    219   sU       �          0    16690    roles 
   TABLE DATA           )   COPY public.roles (id, name) FROM stdin;
    public               user    false    230   �U       �          0    16414    sessions 
   TABLE DATA           _   COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
    public               user    false    220   �U       �          0    16694    shared_notes 
   TABLE DATA           S   COPY public.shared_notes (id, note_id, shared_with_user_id, shared_at) FROM stdin;
    public               user    false    231   �W       �          0    16699    users 
   TABLE DATA           a   COPY public.users (id, login, email, password, role_id, profile_picture, created_at) FROM stdin;
    public               user    false    232   �Y       �           0    0    failed_jobs_id_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);
          public               user    false    226            �           0    0    jobs_id_seq    SEQUENCE SET     :   SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);
          public               user    false    223            �           0    0    migrations_id_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.migrations_id_seq', 3, true);
          public               user    false    217            �           2606    16436    cache_locks cache_locks_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);
 F   ALTER TABLE ONLY public.cache_locks DROP CONSTRAINT cache_locks_pkey;
       public                 user    false    222            �           2606    16429    cache cache_pkey 
   CONSTRAINT     O   ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);
 :   ALTER TABLE ONLY public.cache DROP CONSTRAINT cache_pkey;
       public                 user    false    221            �           2606    16463    failed_jobs failed_jobs_pkey 
   CONSTRAINT     Z   ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);
 F   ALTER TABLE ONLY public.failed_jobs DROP CONSTRAINT failed_jobs_pkey;
       public                 user    false    227            �           2606    16465 #   failed_jobs failed_jobs_uuid_unique 
   CONSTRAINT     ^   ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);
 M   ALTER TABLE ONLY public.failed_jobs DROP CONSTRAINT failed_jobs_uuid_unique;
       public                 user    false    227            �           2606    16708    friends friends_pkey 
   CONSTRAINT     b   ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_pkey PRIMARY KEY (user_id, friend_id);
 >   ALTER TABLE ONLY public.friends DROP CONSTRAINT friends_pkey;
       public                 user    false    228    228            �           2606    16453    job_batches job_batches_pkey 
   CONSTRAINT     Z   ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);
 F   ALTER TABLE ONLY public.job_batches DROP CONSTRAINT job_batches_pkey;
       public                 user    false    225            �           2606    16445    jobs jobs_pkey 
   CONSTRAINT     L   ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);
 8   ALTER TABLE ONLY public.jobs DROP CONSTRAINT jobs_pkey;
       public                 user    false    224            �           2606    16395    migrations migrations_pkey 
   CONSTRAINT     X   ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);
 D   ALTER TABLE ONLY public.migrations DROP CONSTRAINT migrations_pkey;
       public                 user    false    218            �           2606    16710    notes notes_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public.notes DROP CONSTRAINT notes_pkey;
       public                 user    false    229            �           2606    16413 0   password_reset_tokens password_reset_tokens_pkey 
   CONSTRAINT     q   ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);
 Z   ALTER TABLE ONLY public.password_reset_tokens DROP CONSTRAINT password_reset_tokens_pkey;
       public                 user    false    219            �           2606    16712    roles roles_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public.roles DROP CONSTRAINT roles_pkey;
       public                 user    false    230            �           2606    16420    sessions sessions_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);
 @   ALTER TABLE ONLY public.sessions DROP CONSTRAINT sessions_pkey;
       public                 user    false    220            �           2606    16714    shared_notes shared_notes_pkey 
   CONSTRAINT     \   ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_pkey PRIMARY KEY (id);
 H   ALTER TABLE ONLY public.shared_notes DROP CONSTRAINT shared_notes_pkey;
       public                 user    false    231            �           2606    16716    users users_email_key 
   CONSTRAINT     Q   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);
 ?   ALTER TABLE ONLY public.users DROP CONSTRAINT users_email_key;
       public                 user    false    232            �           2606    16718    users users_login_key 
   CONSTRAINT     Q   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_login_key UNIQUE (login);
 ?   ALTER TABLE ONLY public.users DROP CONSTRAINT users_login_key;
       public                 user    false    232            �           2606    16720    users users_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
       public                 user    false    232            �           1259    16446    jobs_queue_index    INDEX     B   CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);
 $   DROP INDEX public.jobs_queue_index;
       public                 user    false    224            �           1259    16422    sessions_last_activity_index    INDEX     Z   CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);
 0   DROP INDEX public.sessions_last_activity_index;
       public                 user    false    220            �           1259    16421    sessions_user_id_index    INDEX     N   CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);
 *   DROP INDEX public.sessions_user_id_index;
       public                 user    false    220            �           2606    16721    friends friends_friend_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_friend_id_fkey FOREIGN KEY (friend_id) REFERENCES public.users(id) ON DELETE CASCADE;
 H   ALTER TABLE ONLY public.friends DROP CONSTRAINT friends_friend_id_fkey;
       public               user    false    232    3306    228            �           2606    16726    friends friends_user_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.friends
    ADD CONSTRAINT friends_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;
 F   ALTER TABLE ONLY public.friends DROP CONSTRAINT friends_user_id_fkey;
       public               user    false    3306    232    228            �           2606    16731    notes notes_user_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.notes
    ADD CONSTRAINT notes_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;
 B   ALTER TABLE ONLY public.notes DROP CONSTRAINT notes_user_id_fkey;
       public               user    false    232    229    3306            �           2606    16736 &   shared_notes shared_notes_note_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_note_id_fkey FOREIGN KEY (note_id) REFERENCES public.notes(id) ON DELETE CASCADE;
 P   ALTER TABLE ONLY public.shared_notes DROP CONSTRAINT shared_notes_note_id_fkey;
       public               user    false    231    229    3296            �           2606    16741 2   shared_notes shared_notes_shared_with_user_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.shared_notes
    ADD CONSTRAINT shared_notes_shared_with_user_id_fkey FOREIGN KEY (shared_with_user_id) REFERENCES public.users(id) ON DELETE CASCADE;
 \   ALTER TABLE ONLY public.shared_notes DROP CONSTRAINT shared_notes_shared_with_user_id_fkey;
       public               user    false    231    232    3306            �           2606    16746    users users_role_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE SET NULL;
 B   ALTER TABLE ONLY public.users DROP CONSTRAINT users_role_id_fkey;
       public               user    false    232    230    3298            �      x������ � �      �      x������ � �      �      x������ � �      �   �   x����C1C�x��g�a�4��#$#�H�J��C��Н��_��ZG,;��kPw������d�N�0c+
�ltC
ʌ4��V����r�LS&�����g���`={�@���N$��M��M��jݍ鏏O�`z��G
Qhx s�ТY=ڍ��ϻ��(�j      �      x������ � �      �      x������ � �      �   E   x�3�4000��"0�O.JM,I�/-N-*�/IL�I�4�2�PhS�����
Wh����0+?	a`� ��$      �     x��Uϊ�6?�<�.��YI�-y��6%���-���Y����<v�TBC����E��e�E�$���%�$��flI����(�6��[��kLb�eFq�Mi&S��E$$�S��(��9O�t�Ei��\�$��s��eW������h������ӿ6O6�\��<0(ۡ�-��P��m�2�=��u�-l�޲Cq����?VE_�X�C[�B6��ulߌ82�Xv�_-�P���KɄX�d��U$D�襔&u�B�[�r�rP�F{$.>�����)ڱl
���>k�vpc}B����'�<l�I!������7K�=�C�u����6�&,��8�:�l^]~��3�����]ᆯ(�U�E���K�t�Z�XM�'�k9Z�2�j뵾�"�t�+�������M>U�F�`����W}'�o�f�E�H�v�
���GV�fմש0?m�@� �9R�潮�:�E�s��߇���nN�g�3�h�0�P�_���t�����8c*�-�����f-�Uj"��2A�;-�
��y&l�S���:4Y*�'�o�\QC�k���P�/���Ѹ� �=ք�r�n��a?E&�Mh���o�8�Ų�[���i��m�Ǵ��2.I�G+v��p�Tvk쭧�9%�L��5��mI�ϧ��tB���ձ����L�wm#��W4�fYe�\z+1@N�,���)�8Iy���.e�[8��0%clf�NcG�s�"�S��,WbK����q��u[��R�����(��pI�S����6�����LQݐ+���&MU��d���0=���%,~�� Fz��������ƚ��5cG�����<A��ZG��&��Kt1M q��:V�R��
H���1�K�9��+`�W��'<��
6?�eS"	�$�.P���m����>�%ձ��9*__�G5�g����5��(W4�:�+����v�"�C�N��k�V��R.c��4���X�#�4�c��ħF(���L@pg3��N929r�OVJm!]����[�y��`��*��eRö��4ʲ�w~�fM�&��.u,���,0*CVj�ld�ѥ�z��b��]�y|mO�����S�j�+i��K��1
my�]~���Ei9Y�U>�ޏ�����Zl�vX���}�Bג�+vg=�n���o��\l���痗U8�^2C��@�̄���L�4��te�)�R�̣D��vZ(ϵN)z.��:p12��"Υ[�c[�ȞlBǟV�5����]�Y��@6�v��) j�^i!T-_-���kO�      �      x������ � �      �   T   x����0�:��(&�]h?G�����I���RE9+"��#��&u���{����)�s�9[�d�w��VG	���؈�qy�      �   �  x�=��n�0���S�l�+
�����4���n��Ɓ�@��Oݦ���}g�F"P��3��|��׺���1ޒbe���Fdd{��L˴G�S\���޴��ۺ;�I�2�GC�}4.н5&��\���w��g:иy}Iq�ǐ�YѺj�[c�;v���� ���x/7��"�"��T"����9(�oW_�ӽ˵�!j��y��V/_ެ*�N1��
�e�:�\l�1���K�y���=��9�����/�
lyf�Ï��O�hqՂ��Yb�=C��fw����c���#�Ȩ�CL j�9yyN��i�[��2ڲ}���J�"���4P��m%�_�ΏY����S/���`����8	2�P�3�P��̦�U��S��8b���b��r���S����%ߔt��U�غ,�	B�d�wH�]6�9��)k�L	'��'���,���_8�< ��9�����N      �   �  x���K�$)EǑ��8el�O��&`���P��Yפ� !����W"��Ž�����d�g�p>�G�Z�HAl��[�<c�[>�c���ʽՅ8#!9B�bꭇ�0`�?��!oB<^̹]��F�_ 1�ƛ�����( �M��$�峟�iGΏHƇ�����6݀��A\n1V�dv�䩜.��A&]��f�K\zp�ٓX�
˷���vvW_<�׸e�L{�k��,��;��l�3a}��U���g�ذM���D�t�;�i��0$�9��;ș���pPU���L�G0��֩U�?I~��_n���5�
h~�XOǹ�Ӥ1�o�G��Bq�B�
�8���c�r:�}y����Dߔ�����/�;�o����E��+����s��Ki:Z�ֳQ��O��k]g��$�-��#�t!���(�?���v��C|�I�#�11����z�~���      �   �  x���Io�0��ү�!W���j+v�d�gA��"��R[V˵��6-PA���7 A̼�Åv�����1�I��"��y�i�6���G�H���/CZ.9˶���-�O����U�x>��Nփ�xH|}��4DZ{`� b�cĝ��\�Q� ��LI#���V�k��e��nB����`���O����k��]���\FS��A���E��D+���p`$�:ӈ�PƃEX
�9�$�������<�ӿa�մ��N��jG�����O�Q=aUu9^)�'���yRLGk7�tl#" �"�Xĭ������`��	��{��y��`m���b"���G6Í�ʲ�HN�%W�S����]^��=�ӳ9���հu�x2�'ˎ������j���������YuS����?h�3�D
l��&�:Jg�5�Kr�nv2@���}k���6={u ��i/騺1���C9.f��Z�v0���ofZ��}���ی'�	�e,,o6�6��M��I���0�t�"=����:�]�j[�����Ҕu����ų�_ȵJ�{.�M�o�����y|�u�a��8}��|¸ME��DRʵ��q�����^� � ����DOnS�^�5���ƍ�xz|��/�*�{�ep����l�=����?�]�˴�'�%O?L���^l���T$���q�e��     