<!--Basic Scripts-->
<script src="/resource/js/jquery-1.12.3.min.js"></script>
<script src="/resource/js/bootstrap.min.js"></script>

<!--Beyond Scripts-->
<script src="/resource/js/beyond.min.js"></script>
<script>
    $(document).ready(function ()
    {
        //导航栏选中效果
        var url = location.href;
        var path_ary = url.split("//");
        var fragment = path_ary[1].split("/");
        if (!fragment[1])
        {
            $('a[href="/log/index"]').parent('li').addClass('active');
            return ;
        }
        var href = fragment[1] + '/' + fragment[2];
        if (href.indexOf('#') == href.length - 1)
        {
            href = href.substring(0, href.indexOf('#'));
        }
        var selector = 'a[href="/' + href + '"]';
        $('li').removeClass('active open');
        var parent = $(selector).parent('li').addClass('active');
        if (parent.parents('li').length > 0)
        {
            parent.parents('li').addClass('active open');
        }
    });
</script>