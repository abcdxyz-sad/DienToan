var express = require('express');
var router = express.Router();
var bcrypt = require('bcryptjs');
var TaiKhoan = require('../models/taikhoan');
const upload = require('../middlewares/uploadAvatar');

// GET: Đăng ký
router.get('/dangky', async (req, res) => {
    res.render('dangky', {
        title: 'Đăng ký tài khoản',
        pageType: 'dangky'
    });
});

// POST: Đăng ký
router.post('/dangky', upload.single('Avatar'), async (req, res) => {
    try {
      // Kiểm tra trùng tên đăng nhập hoặc email
      const tonTai = await TaiKhoan.findOne({
        $or: [
          { TenDangNhap: req.body.TenDangNhap },
          { Email: req.body.Email }
        ]
      });
  
      if (tonTai) {
        req.session.error = 'Tên đăng nhập hoặc email đã tồn tại.';
        return res.redirect('/error');
      }
  
      const salt = bcrypt.genSaltSync(10);
      const data = {
        HoVaTen: req.body.HoVaTen,
        Email: req.body.Email,
        HinhAnh: req.file ? '/uploads/avatar/' + req.file.filename : null,
        TenDangNhap: req.body.TenDangNhap,
        MatKhau: bcrypt.hashSync(req.body.MatKhau, salt)
      };
  
      await TaiKhoan.create(data);
      req.session.success = 'Đã đăng ký tài khoản thành công.';
      res.redirect('/success');
    } catch (err) {
      console.error('Lỗi đăng ký:', err);
      req.session.error = 'Lỗi đăng ký: ' + err.message;
      res.redirect('/dangky');
    }
  });
  

// GET: Đăng nhập
router.get('/dangnhap', async (req, res) => {
    res.render('dangnhap', {
        title: 'Đăng nhập',
        pageType: 'dangnhap'
    });
});

// POST: Đăng nhập
router.post('/dangnhap', async (req, res) => {
    if (req.session.MaNguoiDung) {
        req.session.error = 'Người dùng đã đăng nhập rồi.';
        res.redirect('/error');
    } else {
        var taikhoan = await TaiKhoan.findOne({ TenDangNhap: req.body.TenDangNhap }).exec();
        if (taikhoan) {
            if (bcrypt.compareSync(req.body.MatKhau, taikhoan.MatKhau)) {
                if (taikhoan.KichHoat == 0) {
                    req.session.error = 'Người dùng đã bị khóa tài khoản.';
                    res.redirect('/error');
                } else {
                    // Đăng ký session
                    req.session.MaNguoiDung = taikhoan._id;
                    req.session.HoVaTen = taikhoan.HoVaTen;
                    req.session.QuyenHan = taikhoan.QuyenHan;
                    req.session.Avatar = taikhoan.HinhAnh || '/img/default.png';
                    res.redirect('/');
                }
            } else {
                req.session.error = 'Mật khẩu không đúng.';
                res.redirect('/error');
            }
        } else {
            req.session.error = 'Tên đăng nhập không tồn tại.';
            res.redirect('/error');
        }
    }
});

// GET: Đăng xuất
router.get('/dangxuat', async (req, res) => {
    if(req.session.MaNguoiDung) {
        // Xóa session
        delete req.session.MaNguoiDung;
        delete req.session.HoVaTen;
        delete req.session.QuyenHan;

        res.redirect('/');
    } else {
        req.session.error = 'Người dùng chưa đăng nhập.';
        res.redirect('/error');
    }
});

module.exports = router;