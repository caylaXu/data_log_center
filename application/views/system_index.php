<?php include('layout/header.php'); ?>

<!--Page Related styles-->
<link href="/resource/css/dataTables.bootstrap.css" rel="stylesheet" />

<?php include('layout/body_top.php'); ?>

<!-- Page Content -->
<div class="page-content">
    <div class="page-header position-relative">
        <div class="header-title">
            <h1>
                系统管理
            </h1>
        </div>
        <!--Header Buttons-->
        <div class="header-buttons">
            <a class="sidebar-toggler" href="#">
                <i class="fa fa-arrows-h"></i>
            </a>
            <a class="refresh" id="refresh-toggler" href="">
                <i class="glyphicon glyphicon-refresh"></i>
            </a>
            <a class="fullscreen" id="fullscreen-toggler" href="#">
                <i class="glyphicon glyphicon-fullscreen"></i>
            </a>
        </div>
        <!--Header Buttons End-->
    </div>
    <!-- Page Body -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">
                <div class="widget">
                    <div class="widget-header ">
                        <span class="widget-caption">系统列表</span>
                        <div class="buttons-preview" style="line-height: 35px;">
                            <a id="add" href="#" class="btn btn-primary btn-xs" style="margin-bottom: 0;">添加</a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <table id="datatable" class="table table-striped table-hover table-bordered bootstrap-datatable datatable">
                            <thead>
                            <tr>
                                <th>系统标识</th>
                                <th>系统名称</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Body -->
</div>

<!-- /Page Content -->

<?php include('layout/scripts.php'); ?>

<!--Page Related Scripts-->
<script src="/resource/js/datatable/jquery.dataTables.min.js"></script>
<script src="/resource/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="/resource/js/datalog.datatables.js"></script>
<script src="/resource/js/bootbox/bootbox.js"></script>

<script>
$(document).ready(function()
{
    editModal = '' +
        '<div class="row">' +
        '   <div class="col-md-12">' +
        '       <div class="form-group">' +
        '           <input type="hidden" value="" id="edit_id"/><br/>' +
        '           名称:' +
        '           <input type="text" id="edit_name" maxlength="32" class="form-control" placeholder="(英文+数字)"' +
        '                   style="margin-bottom: 10px;">' +
        '       </div>' +
        '   </div>' +
        '</div>';

    dt_option.ajax.url = '/system/filter';
    dt_option.ajax.data = function (d) {};
    dt_option.columnDefs = [
        {
            "targets": 0,
            "data": "Id",
            "visible": true,
        },
        {
            "targets": 1,
            "data": "Name",
        },
        {
            "targets": -1,
            "data": null,
            "defaultContent": '' +
            '<td>' +
            '<a name="edit" href="#" class="btn btn-primary btn-xs edit"><i class="fa fa-edit"></i> 编辑</a>' +
            '</td>'
        }
    ];
    init_datatables(dt_option);
    url = 'system';
});
</script>

</body>
</html>