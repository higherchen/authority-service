<?php

class Constant
{
    // error code
    const RET_OK = 0;                   // ok
    const RET_SYS_ERROR = 65535;        // 系统错误
    const RET_DATA_CONFLICT = 65534;    // 数据冲突
    const RET_DATA_NO_FOUND = 65533;    // 找不到数据

    const RET_NO_LOGIN = 50001;         // 用户未登陆
    const RET_NO_USER = 50002;          // 用户不存在，请联系管理员
    const RET_USER_NO_ACCESS = 50003;   // 用户无权限

    // auth constant
    const POINT = 1;                    // 权限点
    const CATEGORY = 2;                 // 权限点分类
    const GROUP = 3;                    // 权限组
    const ORG = 4;                      // 组织、管理组
    const ADMIN = 5;                    // ADMIN
}
