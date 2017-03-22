var dt_option = {
    "dom": "t<'row DTTTFooter'<'col-sm-6'i><'col-sm-6'p>>",
    "serverSide": true,
    "ajax": {
        "url": '',
        "dataType": 'json',
        "data": function (d)
        {
            //传递额外的参数给服务器
            d.system_id = $('#system_id').val();
        },
        "type": 'GET',
    },
    "autoWidth": false,
    "stateSave": true,
    "pageLength": 10,
    "ordering": false,
    "processing": true,
    "oLanguage": {
        "sProcessing": "努力加载数据中...",
        "sLengthMenu": "每页显示 _MENU_ 条记录",
        "sInfoEmpty": "没有数据",
        "sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
        "sInfoFiltered": "(从 _MAX_ 条数据中检索)",
        "sZeroRecords": "没有检索到数据",
        "oPaginate": {
            "sFirst": "首页",
            "sPrevious": "前一页",
            "sNext": "后一页",
            "sLast": "尾页"
        }
    },
    "columnDefs": [],
    "initComplete": function (data)
    {
        $('#system_id').bind('change', function ()
        {
            $('#datatable').DataTable().ajax.reload();
        });
    },
};
var table;
var url;

function init_datatables(dt_option)
{
    table = $('#datatable').DataTable(dt_option);
}

var editModal = '' +
    '<div class="row">' +
    '   <div class="col-md-12">' +
    '       <div class="form-group">' +
    '           <input type="hidden" value="" id="edit_id"/><br/>' +
    '           名称:' +
    '           <input type="text" id="edit_name" maxlength="32" class="form-control" placeholder="(英文+数字)"' +
    '                   style="margin-bottom: 10px;">' +
    '           备注:' +
    '           <input type="text" id="edit_mark" maxlength="32" class="form-control" placeholder="(中文)">' +
    '       </div>' +
    '   </div>' +
    '</div>';

var bindModal = '' +
    '<div class="row">' +
    '   <div class="col-md-12">' +
    '       <div class="form-group">' +
    '           <input type="hidden" value="" id="bind_id"/><br/>' +
    '           <ul id="event_list" style="list-style-type: none;margin: 0;">' +
    '           </ul>' +
    '       </div>' +
    '   </div>' +
    '</div>';

var batchModal = '' +
    '<div class="row">' +
    '   <div class="col-md-12">' +
    '       <div class="form-group" style="margin-left: 15%;">' +
    '           <span style="float: left;margin-right: 20px;">批量数据:</span>' +
    '           <textarea style="max-width: 300px;" type="text" value="" id="modal_batch_data"' +
    '               rows="16" cols="45" placeholder="名称,备注"></textarea>' +
    '       </div>' +
    '   </div>' +
    '</div>';

//添加模态框
$('#add').click(function ()
{
    bootbox.dialog({
        message: editModal,
        title: "添加",
        className: "modal-primary",
        buttons: {
            "Close": {
                className: "btn-warning",
                callback: function ()
                {}
            },
            success: {
                label: "Submit",
                className: "btn-blue",
                callback: function ()
                {
                    edit();
                }
            }
        }
    });
});

//编辑模态框
$('#datatable tbody').on('click', "a[name=edit]", function ()
{
    bootbox.dialog({
        message: editModal,
        title: "编辑",
        className: "modal-primary",
        buttons: {
            "Close": {
                className: "btn-warning",
                callback: function ()
                {}
            },
            success: {
                label: "Submit",
                className: "btn-blue",
                callback: function ()
                {
                    if (console.log(edit(url)))
                    {
                        return false;
                    }

                    return true;
                }
            }
        }
    });

    var data = table.row($(this).parents('tr')).data();
    $('#edit_id').val(data['Id']);
    $('#edit_name').val(data['Name']);
    $('#edit_mark').val(data['Mark']);
});

function edit()
{
    var id = $('#edit_id').val();
    var name = $('#edit_name').val()
    var mark = $('#edit_mark').val();

    if (id == '')
    {
        var action = 'add';
        var system_id = $('#system_id').val();
        var data = {system_id: system_id, name: name, mark: mark}
    }
    else
    {
        var action = 'edit';
        var data = {id: id, name: name, mark: mark}
    }

    $.post("/" + url + "/" + action, data, function (result)
    {
        if (result.error)
        {
            alert(result.error);
            return false;
        }
        else
        {
            $('#datatable').DataTable().ajax.reload();
            return true;
        }
    });
}

//绑定模态框
$('#datatable tbody').on('click', "a[name=bind]", function ()
{
    var data = table.row($(this).parents('tr')).data();
    $('#bind_id').val(data['Id']);
    var param = {id: data['Id']};
    $.getJSON('/event/fetch', param, function (result)
    {
        bootbox.dialog({
            message: bindModal,
            title: "绑定",
            className: "modal-primary",
            buttons: {
                "Close": {
                    className: "btn-warning",
                    callback: function ()
                    {
                    }
                },
                success: {
                    label: "Submit",
                    className: "btn-blue",
                    callback: function ()
                    {
                        var events = new Array();
                        $(":input[name='event_list']:checked").each(function ()
                        {
                            events.push($(this).val());
                        });

                        var id = $('#bind_id').val();
                        var param = {id: id, events: events};
                        $.post('/' + url + '/bind_events', param, function (result)
                        {
                            if (result.status != 0)
                            {
                                alert(result.message);
                            }
                            else
                            {
                                $('#bind_modal').modal('hide');
                                $('#datatable').DataTable().ajax.reload();
                            }
                        });
                    }
                },
            }
        });

        $('#bind_id').val(param.id);

        var data = result.data;
        var parent = $('#event_list');
        var child = '';
        parent.children().remove();
        for (var i = 0; i < data.length; i++)
        {
            child = '<label style="margin: 0 8px;">' +
                '<input type="checkbox" name="event_list" id="event_' + data[i]['Id'] + '" class="colored-blue" ' +
                data[i]['checked'] + ' value="' + data[i]['Id'] + '">' +
                '<span class="text">' + data[i]['Mark'] + '</span>' +
                '</label>';
            parent.append(child);
        }
    });
});

//批量弹出框
$('#add_batch').click(function ()
{
    bootbox.dialog({
        message: batchModal,
        title: "批量添加",
        className: "modal-primary",
        buttons: {
            "Close": {
                className: "btn-warning",
                callback: function ()
                {}
            },
            success: {
                label: "Submit",
                className: "btn-blue",
                callback: function ()
                {
                    var system_id = $('#system_id').val();
                    var addr_types = $('#modal_batch_data').val();
                    var data = {system_id: system_id, addr_types: addr_types};

                    $.post('/' + url + '/add_batch', data, function (data)
                    {
                        if (data.status != 0)
                        {
                            alert(data.message);
                        }
                        else
                        {
                            $('#batchModal').modal('hide');
                            $('#datatable').DataTable().ajax.reload();
                        }
                    });
                }
            }
        }
    });
});

//解除绑定
$('#datatable tbody').on('click', '[name=unbind]', function ()
{
    var data = table.row($(this).parents('tr')).data();
    var addr_id = data['Id'];
    var event_id = $(this).val();
    var param = {addr_id: addr_id, event_id: event_id};

    $.post('/' + url + '/unbind_event', param);
});
