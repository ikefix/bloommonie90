@extends($layout)

@section($section)

<style>
    /* =============================
   COMPLAINT PREMIUM STYLING
============================= */

.complaint-wrapper {
    display: flex;
    justify-content: center;
    padding: 60px 20px;
}

.complaint-card {
    width: 100%;
    max-width: 600px;
    background: #ffffff;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    transition: 0.3s ease;
}

.complaint-card:hover {
    transform: translateY(-5px);
}

.complaint-card h2 {
    font-size: 26px;
    margin-bottom: 10px;
}

.complaint-subtext {
    color: #666;
    margin-bottom: 30px;
    font-size: 14px;
}

.form-group {
    margin-bottom: 25px;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

textarea {
    resize: none;
    min-height: 120px;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    transition: 0.3s ease;
    font-size: 14px;
}

textarea:focus {
    outline: none;
    border-color: #000;
    box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
}

input[type="file"] {
    padding: 10px;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    background: #fafafa;
    cursor: pointer;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: #000;
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s ease;
}

.submit-btn:hover {
    background: #c6ff00;
    color: #000;
}
</style>

<div class="complaint-wrapper">

    <div class="complaint-card">
        <h2>Report Inventory Issue</h2>
        <p class="complaint-subtext">
            Describe the issue clearly. Attach screenshot if necessary.
        </p>

        <form action="{{ route('complaints.store') }}" 
              method="POST" 
              enctype="multipart/form-data">

            @csrf  

            <div class="form-group">
                <label>Issue Description</label>
                <textarea 
                    name="message"
                    placeholder="Explain what went wrong in the inventory system..."
                    required></textarea>
            </div>

            <div class="form-group">
                <label>Upload Screenshot (Optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" class="submit-btn">
                Submit Report
            </button>

        </form>
    </div>

</div>

@endsection