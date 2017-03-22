$.datetimepicker.setLocale('zh');

function myGetDate(offset)
{
    var zdate = new Date();
    var edate = new Date(zdate.getTime() + (offset * 24 * 60 * 60 * 1000));
    var year = edate.getFullYear();
    var month = edate.getMonth() >= 9 ? edate.getMonth() + 1 : '0' + (edate.getMonth() + 1);
    var date = edate.getDate() >= 10 ? edate.getDate() : '0' + edate.getDate();
    var str = year + '-' + month + '-' + date;
    return str;
}