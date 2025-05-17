const multer = require('multer');
const path = require('path');

// Cấu hình nơi lưu file
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    cb(null, 'public/uploads/avatar'); // thư mục lưu file
  },
  filename: function (req, file, cb) {
    cb(null, Date.now() + '-' + file.originalname); // Thêm timestamp để tránh trùng tên
  }
});

// Chỉ cho phép ảnh
const fileFilter = (req, file, cb) => {
  if (file.mimetype.startsWith('image/')) cb(null, true);
  else cb(new Error('Chỉ cho phép upload ảnh'), false);
};

const upload = multer({ storage: storage, fileFilter: fileFilter });

module.exports = upload;
