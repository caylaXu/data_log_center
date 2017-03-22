<?php include('layout/header.php'); ?>

<!--Page Related styles-->
<link href="/resource/css/jquery.datetimepicker.css" rel="stylesheet" />
<style>
    .filters {
        margin-bottom: 20px;
    }
    .filters label{
        margin-right: 10px;
    }
    .filters input{
        margin: 0 5px;
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
                日志分析
            </h1>
        </div>
        <!--Header Buttons-->
        <div class="header-buttons">
            <a class="sidebar-toggler" href="#">
                <i class="fa fa-arrows-h"></i>
            </a>
            <a class="" id="reload" href="">
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
            <div class="col-xs-12 col-md-12" style="min-width: 930px;">
                <div class="well">
                    <div class="bordered-blue">
                        <div class="filters">
                            <div>
                                系统：
                                <select type="text" id="system_id">
                                    <?php if (is_array($systems) && count($systems) > 0)
                                    { ?>
                                        <option value="0">请选择</option>
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
                            <div>
                                事件：
                                <select type="text" id="event_id">
                                    <option value="0">请选择</option>
                                    <?php if (is_array($event_types) && count($event_types) > 0)
                                    { ?>
                                        <?php foreach ($event_types as $event_type): ?>
                                        <option value="<?php echo $event_type['Id']; ?>">
                                            <?php echo $event_type['Mark']; ?>
                                        </option>
                                    <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div>
                                地点：
                                <select type="text" id="addr_id">
                                    <option value="0">请选择</option>
                                    <?php if (is_array($addr_types) && count($addr_types) > 0)
                                    { ?>
                                        <?php foreach ($addr_types as $addr_type): ?>
                                        <option value="<?php echo $addr_type['Id']; ?>">
                                            <?php echo $addr_type['Mark']; ?>
                                        </option>
                                    <?php endforeach ?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="filters">
                            <div>
                                时间：
                                <label>
                                    <input name="datepicker" id="datetime_0" value="0" type="radio">
                                    <span class="text">今天 </span>
                                </label>
                                <label>
                                    <input name="datepicker" id="datetime_7" value="7" type="radio" checked="checked">
                                    <span class="text">最近7天 </span>
                                </label>
                                <label>
                                    <input name="datepicker" id="datetime_30" value="30" type="radio">
                                    <span class="text">最近30天 </span>
                                </label>
                                <input type="text" class="form-control" id="datepicker1" style="width: auto;display: inline-block;">
                                <span>至</span>
                                <input type="text" class="form-control" id="datepicker2" style="width: auto;display: inline-block;">
                                <input type="button" class="btn btn-default shiny" id="previous" value="前一天">
                                <input type="button" class="btn btn-default shiny" id="next" value="后一天">
                            </div>
                        </div>
                        <div class="filters">
                            属性列表：
                            <div id="attr_list">
                                <label>暂无属性</label>
                            </div>
                        </div>
                        <div>
                            <div class="" id="chart_pie" style="width: 30%;display: inline-block;">
                            </div>
                            <div class="" id="chart_spline" style="width: 68%;display: inline-block;margin-left: 1%;">
                            </div>
                        </div>
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
<script src="/resource/js/highcharts.js"></script>
<script src="/resource/js/highcharts_exporting.js"></script>
<script src="/resource/js/jquery.datetimepicker.full.min.js"></script>
<script src="/resource/js/datalog.datetimepicker.js"></script>

<script>
    $(document).ready(function ()
    {
        $('#datepicker1').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            todayButton: false,
            onSelectDate: function(current_time, input) {
                $("input[name='datepicker']:checked").attr('checked', false);
                get_attributes();
            }
        });

        $('#datepicker2').datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            todayButton: false,
            onSelectDate: function(current_time, input) {
                $("input[name='datepicker']:checked").attr('checked', false);
                get_attributes();
            }
        });

        $('#datepicker1').val(myGetDate('-6'));
        $('#datepicker2').val(myGetDate('0'));

        $("input[name='datepicker']").bind('change', function ()
        {
            var value = $("input[name='datepicker']:checked").val();
            if (value == 0)
            {
                $('#datepicker1').val(myGetDate('0'));
                $('#datepicker2').val(myGetDate('0'));
            }
            else if (value == 7)
            {
                $('#datepicker1').val(myGetDate('-6'));
                $('#datepicker2').val(myGetDate('0'));
            }
            else if (value == 30)
            {
                $('#datepicker1').val(myGetDate('-29'));
                $('#datepicker2').val(myGetDate('0'));
            }
            get_attributes();
        });

        $('#previous').bind('click', function ()
        {
            if ($("input[name='datepicker']:checked").length > 0)
            {
                $("input[name='datepicker']:checked").attr('checked', false);
            }

            //前一天
            var date1 = $("#datepicker1").val();
            var d = new Date(+new Date(date1) - 1000 * 60 * 60 * 24);
            date1 = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            $("#datepicker1").val(date1);
            $("#datepicker2").val(date1);

            get_attributes();
        });

        $('#next').bind('click', function ()
        {
            if ($("input[name='datepicker']:checked").length > 0)
            {
                $("input[name='datepicker']:checked").attr('checked', false);
            }

            //后一天
            var date1 = $("#datepicker1").val();
            var d = new Date(+new Date(date1) + 1000 * 60 * 60 * 24);
            date1 = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
            $("#datepicker1").val(date1);
            $("#datepicker2").val(date1);

            get_attributes();
        });

        $('#system_id').bind('change', function ()
        {
            var system_id = $('#system_id').val();
            var param = {system_id: system_id};

            $.get('/event/get_list', param, function (data)
            {
                var parent = $('#event_id');
                var child = '<option value="0">请选择</option>';
                parent.children().remove();
                parent.append(child);

                if (data.data.length == 0)
                {
                    return;
                }

                var event_type = data.data;
                for (var i = 0; i < event_type.length; i++)
                {
                    child = '<option value="' + event_type[i].Id + '">' + event_type[i].Mark + '</option>';
                    parent.append(child);
                }
            });

            $.get('/address/get_list', param, function (data)
            {
                var parent = $('#addr_id');
                var child = '<option value="0">请选择</option>';
                parent.children().remove();
                parent.append(child);

                if (data.data.length == 0)
                {
                    return;
                }

                var addr_type = data.data;
                for (var i = 0; i < addr_type.length; i++)
                {
                    child = '<option value="' + addr_type[i].Id + '">' + addr_type[i].Mark + '</option>';
                    parent.append(child);
                }
            });

            get_attributes();
        });

        $('#event_id').bind('change', function ()
        {
            get_attributes();
        });

        $('#addr_id').bind('change', function ()
        {
            get_attributes();
        });

        //获取事件属性
        function get_attributes()
        {
            var system_id = $('#system_id').val();
            var event_id = $('#event_id').val();
            var addr_id = $('#addr_id').val();
            var start = $('#datepicker1').val();
            var end = $('#datepicker2').val();

            if (system_id <= 0 || event_id <= 0)
            {
                return ;
            }
            if (Date.parse(start) > Date.parse(end))
            {
                alert('请正确选择起止日期');
                return ;
            }

            if (Date.parse(start) > Date.parse(end))
            {
                alert('请正确选择起止日期');
                return ;
            }

            var param = {system_id: system_id, event_id: event_id, addr_id: addr_id, start: start, end: end};
            $.get('/attribute/get_attributes', param, function (data)
            {
                var attributes = data['data'];
                var parent = $('#attr_list');
                var child = '';
                parent.children().remove();
                if (!data.data)
                {
                    parent.append('<label>暂无属性</label>');
                    return ;
                }

                if (attributes.length == 0)
                {
                    return ;
                }
                for (var i = 0; i < attributes.length; i++)
                {
                    child = '<label>' +
                        '<input name="attr_id" value="' + attributes[i]['Id'] + '" type="radio" ' +
                        (i==0?'checked="checked"':"") + '>' +
                        '<span class="text">' + attributes[i]['Mark'] + ' </span>' +
                        '</label>';
                    parent.append(child);
                }
                $('input[name="attr_id"]').bind('click', generate_chart);
                generate_chart();
            });
            $('#chart_pie').children().remove();
            $('#chart_spline').children().remove();
        }

        var chart_pie_option = {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: null
            },
            title: {
                text: ''
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage, 1) +'% ('+
                        Highcharts.numberFormat(this.y, 0, ',') + ')';
                },
//                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(1) +' %';
                        }
                    },
                    showInLegend: true,
                }
            },
            series: [{
                type: 'pie',
                name: 'percent',
                data: []
            }],
            credits: {
                enabled: false,
            }
        };

        var chart_spline_option = {
            chart: {
                type: 'spline',
                renderTo: "chart_payment"
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: [],
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            tooltip: {
                crosshairs: {
                    width: 1,
                    color: 'gray',
                },
                shared: true
            },
            series: [],
            credits: {
                enabled: false,
            }
        };

        //生成图表
        function generate_chart()
        {
            var date1 = $('#datepicker1').val();
            var date2 = $('#datepicker2').val();
            var system_id = $('#system_id').val();
            var event_id = $('#event_id').val();
            var addr_id = $('#addr_id').val();
            var attr_id = $('input[name="attr_id"]:checked').val();

            if (system_id == 0 || event_id == 0)
            {
                return ;
            }
            if (Date.parse(date1) > Date.parse(date2))
            {
                alert('请正确选择起止日期');
                return ;
            }

            var param = {date1: date1, date2: date2, system_id: system_id,
                event_id: event_id, addr_id: addr_id, attr_id: attr_id};
            $.get('/analysis/get_event_attrs', param, function (data)
            {
                var parent = $('#chart_spline');
                var child = '';
                parent.children().remove();
                var event_attrs = data.event_attrs;
                var attrs = data.attrs;
                if (event_attrs.length == 0)
                {
                    return;
                }

                var style = '';
                var id = '';
                for (var i = 0; i < event_attrs.length; i++)
                {
                    if (i % 2 != 0)
                    {
                        style = 'style="margin-left: 2%"';
                    }
                    else
                    {
                        style = 'style="margin-left: 0"';
                    }
                    id = 'chart_' + i;
                    child = create_chart(style, id);
                    parent.append(child);

                    var pie_total = 0;
                    var pie_array = new Array();
                    for (var key in event_attrs[i])
                    {
                        pie_array[key] = 0;
                        for (var j = 0; j < event_attrs[i][key].length; j++)
                        {
                            pie_array[key] += parseInt(event_attrs[i][key][j]);
                        }
                        pie_total += pie_array[key];
                    }

                    var pie_series = new Array();
                    var spline_series = new Array();
                    for (var key in event_attrs[i])
                    {
                        pie_series.push([key, pie_array[key]]);
                        spline_series.push({name: key, data: event_attrs[i][key]});
                    }

                    chart_pie_option.title.text = attrs[i];
                    chart_pie_option.series[0].data = pie_series;
                    $('#chart_pie').highcharts(chart_pie_option);

                    chart_spline_option.chart.renderTo = id;
                    chart_spline_option.title.text = attrs[i];
                    chart_spline_option.xAxis.categories = data.categories;
                    chart_spline_option.series = spline_series;
                    new Highcharts.Chart(chart_spline_option);
                }
            });
        }

        function create_chart(style, id)
        {
            var str = '<div class="box span6"' + style + '>' +
                '   <div class="box-header" data-original-title>' +
                '       <h2>' +
                '           <i class="halflings-icon list-alt"></i>' +
                '       </h2>' +
                '       <div class="box-icon">' +
                '           <a href="#" class="btn-setting" id="">' +
                '           </a>' +
                '       </div>' +
                '   </div>' +
                '   <div class="chart_content">' +
                '       <div class="charts" id="' + id + '"></div>' +
                '   </div>' +
                '</div>';

            return str;
        }
    });
</script>

</body>
</html>