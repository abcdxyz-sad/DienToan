var express = require('express');
var router = express.Router();
var GhiChu = require('../models/ghichu');

// Trang chủ - chỉ hiển thị nếu đã đăng nhập
router.get('/', async (req, res) => {
  let gc = [];

  if (req.session.MaNguoiDung) {
    gc = await GhiChu.find({ TaiKhoan: req.session.MaNguoiDung }).exec();
  }

  res.render('index', {
    title: 'Trang chủ',
    ghichu: gc,
    isLoggedIn: !!req.session.MaNguoiDung,
    pageType: 'trangchu'
  });
});


// GET: Lỗi
router.get('/error', async (req, res) => {
    res.render('error', {
        title: 'Lỗi',
        pageType: 'error'
    });
});

// GET: Thành công
router.get('/success', async (req, res) => {
    res.render('success', {
        title: 'Hoàn thành',
        pageType: 'success'
    });
});

module.exports = router;