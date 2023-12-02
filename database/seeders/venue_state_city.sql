CREATE TABLE `venue_state_cities` (
  `id` bigint unsigned not null auto_increment primary key,
  `venue_id` bigint unsigned not null,
  `country_id` bigint unsigned not null,
  `state_id` bigint unsigned not null,
  `city_id` bigint unsigned not null,
  `country_image` varchar(255) null,
  `city_image` varchar(255) null,
  `state_image` varchar(255) null,
  `combination_name` varchar(255) null,
  `created_at` timestamp null default null,
  `updated_at` timestamp null default null,
  foreign key (`country_id`) references `countries` (`id`) on delete cascade on update cascade,
  foreign key (`state_id`) references `states` (`id`) on delete cascade on update cascade,
  foreign key (`city_id`) references `cities` (`id`) on delete cascade on update cascade,
  foreign key (`venue_id`) references `venues` (`id`) on delete cascade on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;