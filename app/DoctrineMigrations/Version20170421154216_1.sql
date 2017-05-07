create table attendance
(
	id bigint auto_increment
		primary key,
	event_id bigint null,
	status varchar(20) null,
	late time null,
	strike_id bigint null,
	created_on datetime null
)
;

create index attendance_event_id_fk
	on attendance (event_id)
;

create index attendance_strike_id_fk
	on attendance (strike_id)
;

create table blog
(
	id bigint auto_increment
		primary key,
	author_id bigint null,
	created_at datetime null,
	updated_at datetime null,
	category varchar(60) null,
	post_exceprt text null,
	status varchar(40) null,
	is_pinned tinyint null,
	content blob null
)
;

create index blog_user_id_fk
	on blog (author_id)
;

create table blog_comment
(
	id bigint auto_increment
		primary key,
	blog_id bigint not null,
	comment_id bigint not null,
	constraint blog_comment_blog_id_fk
		foreign key (blog_id) references chapman_radio.blog (id)
)
;

create index blog_comment_blog_id_fk
	on blog_comment (blog_id)
;

create index blog_comment_comment_id_fk
	on blog_comment (comment_id)
;

create table blog_image
(
	id bigint auto_increment
		primary key,
	blog_id bigint null,
	image_id bigint null,
	constraint blog_image_blog_id_fk
		foreign key (blog_id) references chapman_radio.blog (id)
)
;

create index blog_image_blog_id_fk
	on blog_image (blog_id)
;

create index blog_image_image_id_fk
	on blog_image (image_id)
;

create table comment
(
	id bigint auto_increment
		primary key,
	user_id bigint not null,
	created_at datetime null,
	update_at datetime null,
	content text null,
	comment_id bigint null,
	constraint comment_comment_id_id_fk
		foreign key (comment_id) references chapman_radio.comment (id)
)
;

create index comment_comment_id_id_fk
	on comment (comment_id)
;

create index comment_user_id_fk
	on comment (user_id)
;

alter table blog_comment
	add constraint blog_comment_comment_id_fk
		foreign key (comment_id) references chapman_radio.comment (id)
;

create table dj
(
	id bigint auto_increment
		primary key,
	user_id bigint not null,
	description blob null,
	strike_count int null,
	attend_workshop tinyint null,
	constraint dj_user_id_uindex
		unique (user_id)
)
;

create table dj_image
(
	id bigint auto_increment
		primary key,
	dj_id bigint not null,
	image_id bigint not null,
	constraint dj_image_dj_id_fk
		foreign key (dj_id) references chapman_radio.dj (id)
)
;

create index dj_image_dj_id_fk
	on dj_image (dj_id)
;

create index dj_image_image_id_fk
	on dj_image (image_id)
;

create table event
(
	id bigint not null
		primary key,
	show_schedule_id bigint null,
	start datetime null,
	end datetime null,
	show_id bigint null,
	constraint event_id_uindex
		unique (id)
)
;

create index `_event_show_id_fk`
	on event (show_id)
;

create index `_event_show_schedule_id_fk`
	on event (show_schedule_id)
;

alter table attendance
	add constraint attendance_event_id_fk
		foreign key (event_id) references chapman_radio.event (id)
;

create table image
(
	id bigint auto_increment
		primary key,
	source varchar(200) not null,
	mimetype varchar(30) null,
	created_at datetime null
)
;

alter table blog_image
	add constraint blog_image_image_id_fk
		foreign key (image_id) references chapman_radio.image (id)
;

alter table dj_image
	add constraint dj_image_image_id_fk
		foreign key (image_id) references chapman_radio.image (id)
;


create table recording
(
	id bigint auto_increment
		primary key,
	show_id bigint null,
	event_id bigint null,
	source varchar(100) null,
	short_name varchar(80) null,
	downloads int null,
	created_on datetime null,
	description text null,
	constraint recording_event_id_fk
		foreign key (event_id) references chapman_radio.event (id)
)
;

create index recording_event_id_fk
	on recording (event_id)
;

create index recording_show_id_fk
	on recording (show_id)
;

create table `show`
(
	id bigint auto_increment
		primary key,
	name varchar(100) not null,
	description blob not null,
	created_at datetime not null,
	score int not null,
	profanity tinyint(1) default '0' not null,
	attendanceoptional tinyint(1) default '0' not null,
	updated_at datetime null,
	created_on datetime null,
	genre varchar(80) null,
	header_imge_id bigint null,
	strike_count int null,
	suspended tinyint null,
	enable_comments tinyint default '0' null
)
;

alter table event
	add constraint _event_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id)
;

alter table recording
	add constraint recording_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id)
;

create table show_comment
(
	id bigint auto_increment
		primary key,
	show_id bigint null,
	comment_id bigint null,
	constraint show_comment_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id),
	constraint show_comment_comment_id_fk
		foreign key (comment_id) references chapman_radio.comment (id)
)
;

create index show_comment_comment_id_fk
	on show_comment (comment_id)
;

create index show_comment_show_id_fk
	on show_comment (show_id)
;

create table show_image
(
	id bigint auto_increment
		primary key,
	show_id bigint not null,
	image_id bigint not null,
	constraint show_image_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id),
	constraint show_image_image_id_fk
		foreign key (image_id) references chapman_radio.image (id)
)
;

create index show_image_image_id_fk
	on show_image (image_id)
;

create index show_image_show_id_fk
	on show_image (show_id)
;

create table show_schedule
(
	id bigint auto_increment
		primary key,
	start_time time null,
	end_time time null,
	show_id bigint null,
	constraint event_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id)
)
;

create index event_show_id_fk
	on show_schedule (show_id)
;

alter table event
	add constraint _event_show_schedule_id_fk
		foreign key (show_schedule_id) references chapman_radio.show_schedule (id)
;

create table show_schedule_meta
(
	id bigint auto_increment
		primary key,
	event_id bigint null,
	meta_key varchar(20) null,
	meta_value bigint null,
	constraint event_meta_event_id_fk
		foreign key (event_id) references chapman_radio.show_schedule (id)
)
;

create index event_meta_event_id_fk
	on show_schedule_meta (event_id)
;

create table show_user
(
	id bigint auto_increment
		primary key,
	show_id bigint not null,
	user_id bigint not null,
	constraint show_user_show_id_fk
		foreign key (show_id) references chapman_radio.`show` (id)
)
;

create index show_user_show_id_fk
	on show_user (show_id)
;

create index show_user_user_id_fk
	on show_user (user_id)
;

create table strike
(
	id bigint not null
		primary key,
	user_id bigint null,
	created_on datetime null,
	reason text null,
	email_student tinyint null,
	type varchar(20) null,
	constraint strike_id_uindex
		unique (id)
)
;

create index strike_user_id_fk
	on strike (user_id)
;

alter table attendance
	add constraint attendance_strike_id_fk
		foreign key (strike_id) references chapman_radio.strike (id)
;

create table user
(
	id bigint auto_increment
		primary key,
	facebook_id bigint not null,
	email varchar(100) not null,
	student_id varchar(15) not null,
	phone varchar(30),
	name varchar(120) not null,
	last_login datetime null,
	password varchar(200) not null,
	suspended tinyint(1) default '0' not null,
	created_at datetime null,
	updated_at datetime null,
	username varchar(30) null,
	confirmed tinyint default '0' not null,
	confirmation_token varchar(30) null
)
;

alter table blog
	add constraint blog_user_id_fk
		foreign key (author_id) references chapman_radio.user (id)
;

alter table comment
	add constraint comment_user_id_fk
		foreign key (user_id) references chapman_radio.user (id)
;

alter table dj
	add constraint dj_user_id_fk
		foreign key (user_id) references chapman_radio.user (id)
;

alter table show_user
	add constraint show_user_user_id_fk
		foreign key (user_id) references chapman_radio.user (id)
;

alter table strike
	add constraint strike_user_id_fk
		foreign key (user_id) references chapman_radio.user (id)
;

create table user_role
(
	id bigint auto_increment
		primary key,
	user_id bigint not null,
	role varchar(30) not null,
	constraint role_users_id_fk
		foreign key (user_id) references chapman_radio.user (id)
			on delete cascade
)
;

create index role_users_id_fk
	on user_role (user_id)
;

