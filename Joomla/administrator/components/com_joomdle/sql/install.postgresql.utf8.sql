CREATE TABLE IF NOT EXISTS "#__joomdle_bundles" (
  "id" serial,
  "courses" text NOT NULL,
  "cost" float NOT NULL,
  "currency" varchar(32) NOT NULL,
  "name" varchar(255) NOT NULL,
  "description" text NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_course_applications" (
  "id" serial,
  "user_id" int NOT NULL,
  "course_id" int NOT NULL,
  "state" int NOT NULL,
  "application_date" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "confirmation_date" timestamp without time zone DEFAULT '1970-01-01 00:00:00' NOT NULL,
  "motivation" text NOT NULL,
  "experience" text NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_course_forums" (
  "id" serial,
  "moodle_forum_id" int NOT NULL,
  "kunena_forum_id" int NOT NULL,
  "course_id" int NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_course_groups" (
  "id" serial,
  "course_id" int NOT NULL,
  "group_id" int NOT NULL,
  "type" varchar(32) NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_field_mappings" (
  "id" serial,
  "joomla_app" varchar(45) NOT NULL,
  "joomla_field" varchar(45) NOT NULL,
  "moodle_field" varchar(45) NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_mailinglists" (
  "id" serial,
  "course_id" int NOT NULL,
  "list_id" int NOT NULL,
  "type" varchar(32) NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_profiletypes" (
  "id" serial,
  "profiletype_id" int NOT NULL,
  "create_on_moodle" int NOT NULL,
  "moodle_role" int NOT NULL,
  PRIMARY KEY ("id")
);

CREATE TABLE IF NOT EXISTS "#__joomdle_purchased_courses" (
  "id" serial,
  "user_id" int NOT NULL,
  "course_id" int NOT NULL,
  "num" int NOT NULL,
  PRIMARY KEY ("id")
);
