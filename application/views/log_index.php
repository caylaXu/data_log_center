<?php include('layout/header.php'); ?>

<!--Page Related styles-->
<link href="/resource/css/dataTables.bootstrap.css" rel="stylesheet" />
<link href="/resource/css/jquery.datetimepicker.css" rel="stylesheet" />
<style>
    .filters {
        margin-bottom: 20px;
    }
    .filters label{
        margin-right: 10px;
    }
    .filters input{
        width: auto;
        display: inline-block;
    }
    .filters div {
        display: inline-block;
        margin-right: 20px;
    }
</style>

<?php include('layout/body_top.php'); ?>

<!-- Page Content -->
<div class="page-content">
    <div class="page-header position-relative">
        <div class="header-title">
            <h1>
                日志查询
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
            <div class="col-xs-12 col-md-12" style="min-width: 760px;">
                <div class="widget">
                    <div class="widget-header">
                        <span class="widget-caption">日志列表</span>
                        <div class="buttons-preview" style="line-height: 35px;">
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
                                            <option value="0">请选择</option>
                                            <?php foreach ($systems as $system): ?>
                                            <option value="<?php echo $system['Id']; ?>"
                                                <?php echo (isset($system_id)&&$system_id===intval($system['Id']))?'selected':'';?>>
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
                                <div style="">
                                    事件：<input type="text" id="event" class="form-control">
                                </div>
                            </div>
                            <div class="filters" style="margin-bottom: 10px;">
                                <div>
                                    <span>时间：</span>
                                    <input type="text" id="datepicker1" class="form-control">
                                    <span>至</span>
                                    <input type="text" id="datepicker2" class="form-control">
                                </div>
                            </div>
                            <div class="filters" style="margin-bottom: 10px;">
                                <div>
                                    用户ID：<input type="text" id="user_id" class="form-control">
                                </div>
                                <div>
                                    用户类型：
                                    <select type="text" id="user_type">
                                        <option value="0">请选择</option>
                                        <?php if (is_array($user_types) && count($user_types) > 0)
                                        { ?>
                                            <?php foreach ($user_types as $user_type): ?>
                                            <option value="<?php echo $user_type['Id']; ?>">
                                                <?php echo $user_type['Mark']; ?>
                                            </option>
                                        <?php endforeach ?>
                                        <?php }?>
                                    </select>
                                </div>
                                <div>
                                    <label style="margin-bottom: 0;">
                                        关联用户：
                                        <input type="checkbox" id="related">
                                        <span class="text"></span>
                                    </label>
                                </div>
                            </div>
                            <tr>
                                <th>系统ID</th>
                                <th>用户ID</th>
                                <th>用户类型</th>
                                <th>事件</th>
                                <th>事件属性</th>
                                <th>事件描述</th>
                                <th>发生时间</th>
                                <th>发生地点</th>
                                <th>关联用户</th>
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
<script src="/resource/js/jquery.datetimepicker.full.min.js"></script>
<script src="/resource/js/datalog.datetimepicker.js"></script>
<script src="/resource/js/bootbox/bootbox.js"></script>
<script src="/resource/js/datalog.datatables.js"></script>

<script>
$(document).ready(function()
{
    $('#datepicker1').datetimepicker({
        step: 15,
        format: 'Y-m-d H:i',
        todayButton: false,
        onSelectTime: function(current_time, input) {
            $('#datatable').DataTable().ajax.reload();
        }
    });

    $('#datepicker2').datetimepicker({
        step: 15,
        format: 'Y-m-d H:i',
        todayButton: false,
        onSelectTime: function(current_time, input) {
            $('#datatable').DataTable().ajax.reload();
        }
    });

    $('#datepicker1').val(myGetDate('-6') + ' 00:00');
    $('#datepicker2').val(myGetDate('0') + ' 23:59');

    function update_user_type()
    {
        var system_id = $('#system_id').val();
        var data = {system_id: system_id};
        $.get('/user/get_list', data, function (data)
        {
            var user_type = $('#user_type');
            var child = '<option value="0">无</option>';
            user_type.children().remove();
            if (data.user_types.length > 0)
            {
                var rows = data.user_types;
                child = '<option value="0">请选择</option>';
                user_type.append(child);
                for (var i = 0; i < rows.length; i++)
                {
                    child = '<option value="' + data.user_types[i].Id + '">'
                        + data.user_types[i].Mark + '</option>';
                    user_type.append(child);
                }
            }
            else
            {
                user_type.append(child);
            }
        });
    }

    dt_option.ajax.url = '/log/filter';
    dt_option.ajax.data = function (d) {
        //传递额外的参数给服务器
        d.date1 = $('#datepicker1').val();
        d.date2 = $('#datepicker2').val();
        d.system_id = $('#system_id').val();
        d.user_id = $('#user_id').val();
        d.user_type = $('#user_type').val();
        d.related = $('#related').prop('checked') ? '1' : '0';
        d.event = $('#event').val();
    };
    dt_option.pageLength = 20;
    dt_option.columnDefs = [
        {
            "targets": 0,
            "visible": false,
        }
    ];
    dt_option.initComplete = function (data) {

        $('#system_id').bind('change', function ()
        {
            $('#datatable').DataTable().ajax.reload();
            update_user_type();
        });

        if ($('#system_id').val() > 0)
        {
            update_user_type();
        }

        $('#user_id').bind('change', function ()
        {
            $('#datatable').DataTable().ajax.reload();
        });

        $('#user_type').bind('change', function ()
        {
            $('#datatable').DataTable().ajax.reload();
        });

        $('#related').bind('click', function ()
        {
            if ($('#user_id').val() != '' && $('#user_type').val() != '')
            {
                $('#datatable').DataTable().ajax.reload();
            }
        });

        $('#event').bind('change', function ()
        {
            $('#datatable').DataTable().ajax.reload();
        });
    };
    init_datatables(dt_option);
    url = 'log';
});
</script>

</body>
</html>