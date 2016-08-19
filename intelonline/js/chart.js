//折线
function AjaxDataLine(id, type, company_id, account_id, year, month){
    $.get('/index.php/consensusManage/loadStatistics/type/'+type+'/renderTo/'+id+'/company_id/'+company_id+'/account_id/'+account_id+'/year/'+year+'/month/'+month+'/?_='+ Date.parse(new Date()),function(data){
        $("#"+id).html(data);
    });
}
//饼图
function AjaxDataChart(id,type){
    $.get('/index.php/chart/AcSource/type/'+type+'/renderTo/'+id+'/?_='+ Date.parse(new Date()),function(data){
        $("#"+id).html(data);
    });
}
//柱图 渠道分布
function AjaxDataChannel(id,type){
    $.get('/index.php/chart/AcColumn/type/'+type+'/renderTo/'+id+'/?_='+ Date.parse(new Date()),function(data){
        $("#"+id).html(data);
    });
}