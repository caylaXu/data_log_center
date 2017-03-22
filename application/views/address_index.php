<?php include('layout/header.php'); ?>

<!--Page Related styles-->
<link href="/resource/css/dataTables.bootstrap.css" rel="stylesheet" />

<?php include('layout/body_top.php'); ?>

<!-- Page Content -->
<div class="page-content">
    <div class="page-header position-relative">
        <div class="header-title">
            <h1>
                地点管理
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
                        <span class="widget-caption">地点列表</span>
                        <div class="buttons-preview" style="line-height: 35px;">
                            <a id="add" href="#" class="btn btn-primary btn-xs" style="margin-bottom: 0;">添加</a>
                            <a id="add_batch" href="#" class="btn btn-primary btn-xs" style="margin-bottom: 0;">批量添加</a>
                        </div>
                    </div>
                    <div class="widget-body">
                        <table id="datatable" class="table table-striped table-hover table-bordered bootstrap-datatable datatable">
                            <thead>
                            <div class="filters" style="margin-bottom: 10px;">
                                <div>
                                    系统：
                                    <select type="text" id="system_id">
                                        <?php if (is_array($systems) && count($systems) > 0)
                                        { ?>
                                            <?php foreach ($systems as $system): ?>
                                            <option value="<?php echo $system['Id']; ?>"
                                                <?php echo (isset($system_id) && $system_id === intval($system['Id'])) ? 'selected' : ''; ?>>
                                                <?php echo $system['Name']; ?>
                                            </option>
                                        <?php endforeach ?>
                                        <?php }
                                        else
                                        { ?>
                                            <option value="0">未定义</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <tr>
                                <th>地点标识</th>
                                <th>地点名称</th>
                                <th>地点备注</th>
                                <!--<th>关联事件</th>-->
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div style="clear: both;"></div>
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
<script src="/resource/js/datatable/dataTables.tableTools.min.js"></script>
<script src="/resource/js/datatable/dataTables.bootstrap.min.js"></script>
<script src="/resource/js/bootbox/bootbox.js"></script>
<script src="/resource/js/datalog.datatables.js"></script>

<script>
    $(document).ready(function ()
    {
        dt_option.ajax.url = '/address/filter';
        dt_option.columnDefs = [
            {
                "targets": 0,
                "data": "Id",
                "visible": false,
            },
            {
                "targets": 1,
                "data": "Name",
            },
            {
                "targets": 2,
                "data": "Mark",
            },
            /*{
                "targets": 3,
                "render": function (data, type, row)
                {
                    data = row['Events'];
                    var result = '';
                    for (var i = 0; i < data.length; i++)
                    {
                        if (!data[i])
                        {
                            continue;
                        }
                        result += '<div class="alert alert-info" style="display: inline-block;padding: 5px 10px;margin:2px 10px;">' +
                            '<button name="unbind" value="' + data[i]['Id'] + '" type="button" class="close" data-dismiss="alert" style="right: -6px;">×</button>' +
                            '<span>' + data[i]['Mark'] + '</span>' +
                            '</div>';
                    }
                    result += '<a name="bind" class="btn btn-primary btn-xs icon-only" href="javascript:void(0);"><i class="fa fa-lock "></i></a>';
                    return result;
                },
            },*/
            {
                "targets": -1,
                "data": null,
                "defaultContent": '' +
                '<td>' +
                '<a name="edit" href="#" class="btn btn-primary btn-xs edit"><i class="fa fa-edit"></i> 编辑</a>' +
                '</td>'
            }
        ];
        // DataTable
        init_datatables(dt_option);
        url = 'address';
    });
</script>

<?php include('layout/body_bottom.php'); ?>


