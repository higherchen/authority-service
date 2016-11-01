<?php

class Constant
{
    // error code
    const RET_OK = 0;                       // ok
    const RET_SYS_ERROR = 65535;            // 系统错误
    const RET_DATA_CONFLICT = 65534;        // 数据冲突
    const RET_DATA_NO_FOUND = 65533;        // 找不到数据
    const RET_METHOD_ERROR = 65532;         // 请求方式错误
    const RET_UPDATE_FAIL = 65531;          // 更新数据失败（冲突/找不到/未作修改）

    const RET_NO_LOGIN = 50001;             // 用户未登陆
    const RET_NO_USER = 50002;              // 用户不存在，请联系管理员
    const RET_USER_NO_ACCESS = 50003;       // 用户无权限
    const RET_INVALID_USERNAME = 50004;     // 无效用户名
    const RET_INVALID_NICKNAME = 50005;     // 无效昵称
    const RET_INVALID_EMAIL = 50006;        // 无效邮箱
    const RET_INVALID_TELEPHONE = 50007;    // 无效手机号
    const RET_INVALID_RULE_NAME = 50008;    // 无效Rule name
    const RET_INVALID_CATE_NAME = 50009;    // 无效分类名
    const RET_INVALID_GROUP_NAME = 50010;   // 无效组名
    const RET_INVALID_POINT_NAME = 50011;   // 无效权限名
    const RET_INVALID_POINT_DATA = 50012;   // 无效权限代码标识
    const RET_INVALID_ROLE_NAME = 50013;    // 无效角色名

    // auth constant
    const POINT = 1;                        // 权限点
    const CATEGORY = 2;                     // 权限点分类
    const GROUP = 3;                        // 权限组
    const ORG = 4;                          // 组织、管理组
    const ADMIN = 5;                        // ADMIN
}
