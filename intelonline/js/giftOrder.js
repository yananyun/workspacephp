//礼品驳回
function giftOrderReject(obj,id){
	var box=$(obj).parents('li');
	art.dialog({
		id: "giftOrderReject",
		content: '该礼品订单将被驳回，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/giftOrder/refuseGiftOrder/id/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							box.remove();
							artInfo('操作成功');
						}else{
							artInfo(data.info);
						}
						window.location.reload();
					},
					error:function(data){
						process.close();
						artInfo('操作失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});	
}
//礼品通过
function giftOrderPass(obj,id){
	var box=$(obj).parents('li');
	art.dialog({
		id: "giftOrderReject",
		content: '该礼品订单将通过审核，您确定吗？',
		button: [
		{
			name: '确定',
			callback: function () {
				this.close();
				process=loading('Loading...');
				$.ajax({
					url: '/index.php/giftOrder/accessGiftOrder/id/'+ id,
					dataType:'json',
					type:'get',
					success: function (data){
						process.close();
						if(data.status){
							artInfo('操作成功');
							box.remove();
						}else{
							artInfo(data.info);
						}
						window.location.reload();
					},
					error:function(data){
						process.close();
						artInfo('操作失败');
					},
					cache: false
				});
				return false
			},
			focus: true
		},		
		{
			name: '取消'
		}
		],
		lock:true
	});	
}
