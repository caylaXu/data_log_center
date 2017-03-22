</head>
<!-- /Head -->
<!-- Body -->
<body>
<!-- Loading Container -->
<div class="loading-container1">
    <div class="loading-progress">
        <div class="rotator">
            <div class="rotator">
                <div class="rotator colored">
                    <div class="rotator">
                        <div class="rotator colored">
                            <div class="rotator colored"></div>
                            <div class="rotator"></div>
                        </div>
                        <div class="rotator colored"></div>
                    </div>
                    <div class="rotator"></div>
                </div>
                <div class="rotator"></div>
            </div>
            <div class="rotator"></div>
        </div>
        <div class="rotator"></div>
    </div>
</div>
<!--  /Loading Container -->
<!-- Navbar -->
<div class="navbar">
    <div class="navbar-inner">
        <div class="navbar-container">
            <!-- Navbar Barnd -->
            <div class="navbar-header pull-left">
                <a href="#" class="navbar-brand">
                    <small style="margin: 0 20px;line-height: 35px;">
                        <span>日志分析系统</span>
<!--                        <img src="/resource/img/logo.png" alt=""/>-->
                    </small>
                </a>
            </div>
            <!-- /Navbar Barnd -->

            <!-- Sidebar Collapse -->
            <div class="sidebar-collapse" id="sidebar-collapse">
                <i class="collapse-icon fa fa-bars"></i>
            </div>
            <!-- /Sidebar Collapse -->
            <!-- Account Area and Settings --->
            <div class="navbar-header pull-right">
                <div class="navbar-account">
                    <ul class="account-area">
                        <li style="width: 100%;">
                            <a class="login-area dropdown-toggle" data-toggle="dropdown">
                                <div class="avatar" title="View your public profile" style="border: none;">
                                    <img src="/resource/img/avatars/bing.png">
                                </div>
                                <section>
                                    <h2><span class="profile"><span><?php echo $account; ?></span></span></h2>
                                </section>
                            </a>
                            <!--Login Area Dropdown-->
                            <ul class="pull-right dropdown-menu dropdown-arrow dropdown-login-area">
                                <!--Theme Selector Area-->
                                <li class="theme-area">
                                    <ul class="colorpicker" id="skin-changer">
                                        <li><a class="colorpick-btn" href="#" style="background-color:#5DB2FF;"
                                               rel="/resource/css/skins/blue.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#2dc3e8;"
                                               rel="/resource/css/skins/azure.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#03B3B2;"
                                               rel="/resource/css/skins/teal.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#53a93f;"
                                               rel="/resource/css/skins/green.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#FF8F32;"
                                               rel="/resource/css/skins/orange.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#cc324b;"
                                               rel="/resource/css/skins/pink.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#AC193D;"
                                               rel="/resource/css/skins/darkred.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#8C0095;"
                                               rel="/resource/css/skins/purple.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#0072C6;"
                                               rel="/resource/css/skins/darkblue.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#585858;"
                                               rel="/resource/css/skins/gray.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#474544;"
                                               rel="/resource/css/skins/black.min.css"></a></li>
                                        <li><a class="colorpick-btn" href="#" style="background-color:#001940;"
                                               rel="/resource/css/skins/deepblue.min.css"></a></li>
                                    </ul>
                                </li>
                                <!--/Theme Selector Area-->
                                <li class="dropdown-footer">
                                    <a href="login.html">
                                        Sign out
                                    </a>
                                </li>
                            </ul>
                            <!--/Login Area Dropdown-->
                        </li>
                        <!-- /Account Area -->
                        <!--Note: notice that setting div must start right after account area list.
                        no space must be between these elements-->
                        <!-- Settings -->
                    </ul>
                    <!-- Settings -->
                </div>
            </div>
            <!-- /Account Area and Settings -->
        </div>
    </div>
</div>
<!-- /Navbar -->
<!-- Main Container -->
<div class="main-container container-fluid">
    <!-- Page Container -->
    <div class="page-container">
        <!-- Page Sidebar -->
        <div class="page-sidebar" id="sidebar">
            <!-- Page Sidebar Header-->
            <div class="sidebar-header-wrapper">
                <!--<input type="text" class="searchinput"/>
                <i class="searchicon fa fa-search"></i>
                <div class="searchhelper">Search Reports, Charts, Emails or Notifications</div>-->
            </div>
            <!-- /Page Sidebar Header -->
            <!-- Sidebar Menu -->
            <ul class="nav sidebar-menu">
                <li href="test">
                    <a href="/log/index">
                        <i class="menu-icon glyphicon glyphicon-home"></i>
                        <span class="menu-text"> 日志查询 </span>
                    </a>
                </li>
                <li>
                    <a href="/analysis/index">
                        <i class="menu-icon fa fa-bar-chart-o"></i>
                        <span class="menu-text"> 日志分析 </span>
                    </a>
                </li>
                <li class="">
                    <a href="#" class="menu-dropdown">
                        <i class="menu-icon fa fa-table"></i>
                        <span class="menu-text"> 自定义 </span>

                        <i class="menu-expand"></i>
                    </a>

                    <ul class="submenu">
                        <li>
                            <a href="/system/index">
                                <span class="menu-text">系统管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="/user/index">
                                <span class="menu-text">用户管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="/event/index">
                                <span class="menu-text">事件管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="/attribute/index">
                                <span class="menu-text">事件属性管理</span>
                            </a>
                        </li>
                        <li>
                            <a href="/address/index">
                                <span class="menu-text">地点管理</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/system/download">
                        <i class="menu-icon fa fa-download"></i>
                        <span class="menu-text"> 配置文件下载 </span>
                    </a>
                </li>
            </ul>
            <!-- /Sidebar Menu -->
        </div>
        <!-- /Page Sidebar -->