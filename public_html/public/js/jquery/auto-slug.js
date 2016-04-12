function auto_slug(src, dst)
{
    var src_text = $(src).val();
    src_text = src_text.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|_/g,"-");
    src_text = src_text.replace(/^\-+|\-+?&$/g,"");
    src_text = src_text.replace(/[\/\\]/g,"-");
    src_text = src_text.replace(/[-]+/gi,'-');
    src_text = src_text.replace(/[âấầẩẫậăắằẳẵặáàảãạ]/gi,'a');
    src_text = src_text.replace(/[êếềểễệéèẻẽẹ]/gi,'e');
    src_text = src_text.replace(/[óòỏõọôốồổỗộơớờởỡợ]/gi,'o');
    src_text = src_text.replace(/[úùủũụưứừửữự]/gi,'u');
    src_text = src_text.replace(/[ýỳỷỹỵ]/gi,'y');
    src_text = src_text.replace(/[íìỉĩị]/gi,'i');
    src_text = src_text.replace(/[đ]/gi,'d');
	
	src_text = src_text.toLowerCase();
    $(dst).val(src_text);
}