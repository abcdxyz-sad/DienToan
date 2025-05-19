const express = require("express");
const router = express.Router();
const bcrypt = require("bcryptjs");
const fs = require("fs");
const path = require("path");

const TaiKhoan = require("../models/taikhoan");
const upload = require("../middlewares/uploadAvatar");

// GET: Danh sách tài khoản
router.get("/", async (req, res) => {
	const tk = await TaiKhoan.find();
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
router.post("/them", upload.single("Avatar"), async (req, res) => {
	try {
		const tonTai = await TaiKhoan.findOne({
			$or: [
				{ TenDangNhap: req.body.TenDangNhap },
				{ Email: req.body.Email },
			],
		});
		if (tonTai) {
			req.session.error = "Tên đăng nhập hoặc email đã tồn tại.";
			return res.redirect("/error");
		}

		const salt = bcrypt.genSaltSync(10);
		const data = {
			HoVaTen: req.body.HoVaTen,
			Email: req.body.Email,
			HinhAnh: req.file ? "/uploads/avatar/" + req.file.filename : null,
			TenDangNhap: req.body.TenDangNhap,
			MatKhau: bcrypt.hashSync(req.body.MatKhau, salt),
			QuyenHan: req.body.QuyenHan || "user",
		};
		await TaiKhoan.create(data);
		res.redirect("/taikhoan");
	} catch (err) {
		console.error("Lỗi khi thêm tài khoản:", err);
		req.session.error = "Lỗi khi thêm tài khoản: " + err.message;
		res.redirect("/error");
	}
});


// GET: Tài khoản của tôi
router.get("/cuatoi/:id", async (req, res) => {
	const id = req.params.id.trim();
	const tk = await TaiKhoan.findById(id);
	res.render("taikhoan_cuatoi", {
		title: "Hồ sơ cá nhân",
		taikhoan: tk,
	});
});

// GET: Sửa tài khoản
router.get("/sua/:id", async (req, res) => {
	const id = req.params.id;
	const tk = await TaiKhoan.findById(id);
	res.render("taikhoan_sua", {
		title: "Sửa tài khoản",
		taikhoan: tk,
	});
});

// POST: Sửa tài khoản
router.post("/sua/:id", upload.single("Avatar"), async (req, res) => {
	const id = req.params.id;
	const salt = bcrypt.genSaltSync(10);

	const data = {
		HoVaTen: req.body.HoVaTen,
		Email: req.body.Email,
	};

	if (req.body.MatKhau) {
		data["MatKhau"] = bcrypt.hashSync(req.body.MatKhau, salt);
	}

	if (req.file) {
		data["HinhAnh"] = "/uploads/avatar/" + req.file.filename;
	}

	if (req.session && req.session.QuyenHan === "admin") {
		data["TenDangNhap"] = req.body.TenDangNhap;
		data["QuyenHan"] = req.body.QuyenHan;
		data["KichHoat"] = req.body.KichHoat;
	}

	await TaiKhoan.findByIdAndUpdate(id, data);

	if (req.session && req.session.MaNguoiDung === id) {
		req.session.destroy((err) => {
			res.redirect("/dangnhap");
		});
	} else {
		res.redirect("/taikhoan");
	}
});

// GET: Xóa tài khoản (bao gồm xóa avatar)
router.get("/xoa/:id", async (req, res) => {
	const id = req.params.id;
	try {
		// Không cho phép admin đang đăng nhập tự xóa chính mình
		if (req.session && req.session.MaNguoiDung === id && req.session.QuyenHan === "admin") {
			req.session.error = "Bạn đang đăng nhập tài khoản này, không thể xóa";
			return res.redirect("/error");
		}

		const tk = await TaiKhoan.findById(id);
		if (tk && tk.HinhAnh) {
			const avatarPath = path.join(__dirname, "..", "public", tk.HinhAnh);
			if (fs.existsSync(avatarPath)) {
				fs.unlinkSync(avatarPath);
			}
		}

		await TaiKhoan.findByIdAndDelete(id);
		res.redirect("/taikhoan");
	} catch (err) {
		console.error("Lỗi khi xóa tài khoản:", err);
		req.session.error = "Lỗi khi xóa tài khoản: " + err.message;
		res.redirect("/error");
	}
});


// POST: Upload avatar riêng
router.post("/avatar", upload.single("avatar"), async (req, res) => {
	const file = req.file;
	if (!file) {
		return res.status(400).send("Không có file nào được tải lên.");
	}

	const avatarPath = "/uploads/avatar/" + file.filename;

	await TaiKhoan.updateOne({ _id: req.session.MaNguoiDung }, { HinhAnh: avatarPath });
	req.session.Avatar = avatarPath;

	res.redirect("/taikhoan/cuatoi/" + req.session.MaNguoiDung);
});

module.exports = router;