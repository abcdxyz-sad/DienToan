var mongoose = require('mongoose');

const ghiChuSchema = new mongoose.Schema({
	TaiKhoan: { type: mongoose.Schema.Types.ObjectId, ref: 'TaiKhoan' },
	TieuDe: { type: String, required: true },
	NoiDung: { type: String, required: true },
	NgayDang: { type: Date, default: Date.now },
});

var ghiChuModel = mongoose.model('GhiChu', ghiChuSchema);

module.exports = ghiChuModel;