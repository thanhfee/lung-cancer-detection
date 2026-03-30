<form action="{{ route('scans.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label>Chọn Bệnh nhân:</label>
    <select name="patient_id">
        <option value="1">Nguyễn Văn A (BN9167)</option>
    </select>

    <label>Ảnh chụp X-Quang phổi:</label>
    <input type="file" name="lung_image">

    <label>Ghi chú của bác sĩ:</label>
    <textarea name="doctor_comments"></textarea>

    <button type="submit">Gửi phân tích AI</button>
</form>