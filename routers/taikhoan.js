var express = require("express");
var router = express.Router();
var bcrypt = require("bcryptjs");
var TaiKhoan = require("../models/taikhoan");
const upload = require('../middlewares/uploadAvatar');


// GET: Danh sách tài khoản
router.get("/", async (req, res) => {
	var tk = await TaiKhoan.find();
	res.render("taikhoan", {
		title: "Danh sách tài khoản",
		taikhoan: tk,
	});
});

// GET: Thêm tài khoản
router.get("/them", async (req, res) => {
	res.render("taikhoan_them", {
		title: "Thêm tài khoản",
	});
});

// POST: Thêm tài khoản
router.post("/them", upload.single('Avatar'), async (req, res) => {
	var salt = bcrypt.genSaltSync(10);
	var data = {
		HoVaTen: req.body.HoVaTen,
		Email: req.body.Email,
		HinhAnh: req.file ? '/uploads/avatar/' + req.file.filename : null,
		TenDangNhap: req.body.TenDangNhap,
		MatKhau: bcrypt.hashSync(req.body.MatKhau, salt),
		QuyenHan: req.body.QuyenHan || 'user',
	};
	await TaiKhoan.create(data);
	res.redirect("/taikhoan");
});

// GET: tài khoản của tôi
router.get("/cuatoi/:id", async (req, res) => {
	var id = req.params.id.trim();
	var tk = await TaiKhoan.findById(id);
	res.render("taikhoan_cuatoi", {
		title: "Hồ sơ cá nhân",
		taikhoan: tk,
	});
});

// GET: Sửa tài khoản
router.get("/sua/:id", async (req, res) => {
	var id = req.params.id;
	var tk = await TaiKhoan.findById(id);
	res.render("taikhoan_sua", {
		title: "Sửa tài khoản",
		taikhoan: tk,
	});
});

// POST: Sửa tài khoản
router.post("/sua/:id", upload.single('Avatar'), async (req, res) => {
	var id = req.params.id;
	var salt = bcrypt.genSaltSync(10);

	var data = {
		HoVaTen: req.body.HoVaTen,
		Email: req.body.Email,
	};

	if (req.body.MatKhau) {
		data["MatKhau"] = bcrypt.hashSync(req.body.MatKhau, salt);
	}

	if (req.file) {
		data["HinhAnh"] = '/uploads/avatar/' + req.file.filename;
	}

	// Nếu là admin thì cho phép cập nhật thêm thông tin
	if (req.session && req.session.QuyenHan === "admin") {
		data["TenDangNhap"] = req.body.TenDangNhap;
		data["QuyenHan"] = req.body.QuyenHan;
		data["KichHoat"] = req.body.KichHoat;
	}

	await TaiKhoan.findByIdAndUpdate(id, data);

	// Nếu người dùng sửa chính tài khoản của họ → xóa session và đăng xuất
	if (req.session && req.session.MaNguoiDung === id) {
		req.session.destroy((err) => {
			res.redirect("/dangnhap");
		});
	} else {
		res.redirect("/taikhoan");
	}
});


// GET: Xóa tài khoản
router.get("/xoa/:id", async (req, res) => {
	var id = req.params.id;
	await TaiKhoan.findByIdAndDelete(id);
	res.redirect("/taikhoan");
});


// Route POST upload avatar
router.post('/avatar', upload.single('avatar'), async (req, res) => {
	const file = req.file;
	if (!file) {
	  return res.status(400).send('Không có file nào được tải lên.');
	}
  
	// Giả sử bạn lưu avatar vào MongoDB theo đường dẫn
	const avatarPath = '/uploads/avatar/' + file.filename;
  
	// Cập nhật avatar cho người dùng
	// Ví dụ:
	await TaiKhoan.updateOne({ _id: req.session.MaNguoiDung }, { HinhAnh: avatarPath });
	req.session.Avatar = avatarPath;
  
	// Lưu vào session để hiển thị lại
	req.session.Avatar = avatarPath;
  
	res.redirect('/taikhoan/cuatoi/' + req.session.MaNguoiDung);
  });
module.exports = router;
