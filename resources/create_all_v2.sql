CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` varchar(16) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `telephone` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号码',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '用户表';

CREATE TABLE `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(16) NOT NULL COMMENT 'app名称',
  `app_key` varchar(32) NOT NULL COMMENT 'app key',
  `app_secret` varchar(16) NOT NULL DEFAULT '' COMMENT 'app secret',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_apps_1` (`name`),
  UNIQUE KEY `uk_apps_2` (`app_key`),
  UNIQUE KEY `uk_apps_3` (`app_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应用接入表';

CREATE TABLE `auth_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(16) NOT NULL COMMENT '权限、角色名称',
  `type` tinyint(4) NOT NULL COMMENT '区分权限和角色',
  `description` varchar(32) NOT NULL DEFAULT '' COMMENT '描述',
  `app_id` int(11) NOT NULL COMMENT 'app表关联id',
  `mark` varchar(32) NOT NULL DEFAULT '' COMMENT '代码标识段',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_auth_item` (`app_id`,`name`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='储存权限和角色的表';

CREATE TABLE `auth_item_child` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `parent` int(11) NOT NULL COMMENT '父级',
  `child` int(11) NOT NULL COMMENT '子级',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_auth_item_child` (`parent`, `child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色和权限(或角色和角色)的关联表';

CREATE TABLE `auth_assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `item_id` int(11) NOT NULL COMMENT '权限或角色 auth_item ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_auth_assignment` (`item_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色（或权限）和用户的关联表';

CREATE TABLE `resource_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(16) NOT NULL COMMENT '资源名',
  `src_id` int(11) NOT NULL COMMENT '资源ID',
  `owner_id` int(11) NOT NULL DEFAULT 0 COMMENT '所有者ID',
  `role_id` int(11) NOT NULL DEFAULT 0 COMMENT '角色ID',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资源标识';

CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(16) NOT NULL COMMENT '角色名',
  `description` varchar(32) NOT NULL DEFAULT '' COMMENT '描述',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `mtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资源角色';

CREATE TABLE `role_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_member` (`role_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资源组分配';
