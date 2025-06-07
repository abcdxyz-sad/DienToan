var express = require('express');
var router = express.Router();
var GhiChu = require('../models/ghichu');

// GET: Danh sách ghi chú
router.get('/', async (req, res) => {
	var gc = await GhiChu.find()
        .populate('TaiKhoan').exec();
    res.render('ghichu', {
        title: 'Danh sách ghi chú',
        ghichu: gc,
        pageType: 'ghichu'
    });
});

// GET: Lưu ghi chú
router.get('/them', async (req, res) => {
	var gc = await GhiChu.find();
    res.render('ghichu_them', {
        title: 'Lưu ghi chú',
        ghichu: gc,
        pageType: 'ghichu_them'
    });
});

// POST: Lưu ghi chú
router.post('/them', async (req, res) => {
    if(req.session.MaNguoiDung) {
        var data = {
            TaiKhoan: req.session.MaNguoiDung,
            TieuDe: req.body.TieuDe,
            NoiDung: req.body.NoiDung
        };
        await GhiChu.create(data);
        req.session.success = 'Đã lưu ghi chú thành công.';
        res.redirect('/success');
    } else {
        res.redirect('/dangnhap');
    }
});


// GET: Sửa ghi chú
router.get('/sua/:id', async (req, res) => {
    var id = req.params.id;
    var gc = await GhiChu.findById(id);
    res.render('ghichu_sua', {
        title: 'Sửa ghi chú',
        ghichu: gc,
        pageType: 'ghichu_sua'
    });
});

// POST: Sửa ghi chú
router.post('/sua/:id', async (req, res) => {
	var id = req.params.id;
    var data = {
        TieuDe: req.body.TieuDe,
        NoiDung: req.body.NoiDung
    };
    await GhiChu.findByIdAndUpdate(id, data);
    req.session.success = 'Đã cập nhật ghi chú thành công.';
    res.redirect('/success');
});

// GET: Xóa ghi chú
router.get('/xoa/:id', async (req, res) => {
	var id = req.params.id;
    await GhiChu.findByIdAndDelete(id);

    // Trở lại trang trước
    res.redirect(req.get('Referrer') || '/');
});

// GET: Danh sách ghi chú của tôi
router.get('/cuatoi', async (req, res) => {
	if (req.session.MaNguoiDung) {
		const id = req.session.MaNguoiDung;
		const gc = await GhiChu.find({ TaiKhoan: id }).exec();

		res.render('ghichu_cuatoi', {
			title: 'Ghi chú của tôi',
			ghichu: gc,
            pageType: 'ghichu_cuatoi'
		});
	} else {
		res.redirect('/dangnhap');
	}
});

// GET: Xem chi tiết ghi chú
router.get('/chitiet/:id', async (req, res) => {
    var id = req.params.id;
    var gc = await GhiChu.findById(id).populate('TaiKhoan');
    res.render('ghichu_chitiet', {
        title: 'Chi tiết ghi chú',
        ghichu: gc,
        pageType: 'ghichu_chitiet'
    });
}); 

module.exports = router;