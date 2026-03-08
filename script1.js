// Page navigation
function navigateToPage(pageId) {
    document.querySelectorAll('.page').forEach(page => {
        page.style.display = 'none';
        page.classList.remove('active');
    });
    const target = document.getElementById(pageId);
    if (target) {
        target.style.display = 'block';
        target.classList.add('active');
    }
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.page === pageId) link.classList.add('active');
    });
}

// ===== Prescription Functions =====
let medCount = 0;

function addMedication() {
    medCount++;
    const list = document.getElementById('medicationsList');
    const row = document.createElement('div');
    row.className = 'medication-row';
    row.id = 'med-' + medCount;
    row.innerHTML = `
        <div class="form-group">
            <label>Medication Name</label>
            <input type="text" placeholder="e.g. Amoxicillin 500mg">
        </div>
        <div class="form-group">
            <label>Dosage</label>
            <input type="text" placeholder="e.g. 1 tablet">
        </div>
        <div class="form-group">
            <label>Frequency</label>
            <input type="text" placeholder="e.g. 3x/day">
        </div>
        <button class="btn-remove-med" onclick="removeMedication('med-${medCount}')" title="Remove">
            <i class="fas fa-times"></i>
        </button>
    `;
    list.appendChild(row);
}

function removeMedication(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function previewPrescription() {
    const name = document.getElementById('patientName').value || '—';
    const age = document.getElementById('patientAge').value || '—';
    const gender = document.getElementById('patientGender').value || '—';
    const date = document.getElementById('rxDate').value || new Date().toLocaleDateString();
    const diagnosis = document.getElementById('diagnosisText').value || '';
    const notes = document.getElementById('doctorNotes').value || '';

    const meds = [];
    document.querySelectorAll('.medication-row').forEach(row => {
        const inputs = row.querySelectorAll('input');
        if (inputs[0] && inputs[0].value) {
            meds.push({
                name: inputs[0].value,
                dose: inputs[1] ? inputs[1].value : '',
                freq: inputs[2] ? inputs[2].value : ''
            });
        }
    });

    let medsHTML = meds.length === 0
        ? '<p style="color:#aaa;font-style:italic;">No medications added.</p>'
        : meds.map((m, i) => `
            <div class="rx-med-item">
                <div class="rx-med-number">${i + 1}</div>
                <div class="rx-med-info">
                    <strong>${m.name}</strong>
                    <span>${m.dose}${m.freq ? ' — ' + m.freq : ''}</span>
                </div>
            </div>`).join('');

    const diagHTML = diagnosis ? `
        <div class="rx-preview-diagnosis">
            <h4><i class="fas fa-stethoscope"></i> Diagnosis</h4>
            <p>${diagnosis}</p>
        </div>` : '';

    const notesHTML = notes ? `
        <div class="rx-preview-notes">
            <h4><i class="fas fa-notes-medical"></i> Doctor Notes</h4>
            <p>${notes}</p>
        </div>` : '';

    document.getElementById('rxPreviewContent').innerHTML = `
        <div class="rx-preview-header">
            <div class="rx-big-badge">Rx</div>
            <h2>Electronic Prescription</h2>
            <p><i class="fas fa-heartbeat"></i> Health without distance</p>
        </div>
        <div class="rx-preview-patient">
            <span><strong>Patient:</strong> ${name}</span>
            <span><strong>Age:</strong> ${age}</span>
            <span><strong>Gender:</strong> ${gender}</span>
            <span><strong>Date:</strong> ${date}</span>
        </div>
        ${diagHTML}
        <h4 style="margin-bottom:1rem; color:var(--primary-solid);"><i class="fas fa-pills"></i> Medications</h4>
        ${medsHTML}
        ${notesHTML}
        <div class="rx-preview-footer">
            <i class="fas fa-signature"></i> Doctor's Signature
            <br><strong>Dr. <?php echo htmlspecialchars($user['full_name']); ?></strong>
            <br><?php echo htmlspecialchars($user['specialty']); ?>
        </div>
    `;

    const preview = document.getElementById('rxPreview');
    preview.style.display = 'block';
    preview.scrollIntoView({ behavior: 'smooth' });
}

function printPrescription() {
    previewPrescription();
    setTimeout(() => window.print(), 300);
}

function clearPrescription() {
    document.getElementById('patientName').value = '';
    document.getElementById('patientAge').value = '';
    document.getElementById('patientGender').value = '';
    document.getElementById('rxDate').value = '';
    document.getElementById('diagnosisText').value = '';
    document.getElementById('doctorNotes').value = '';
    document.getElementById('medicationsList').innerHTML = '';
    document.getElementById('rxPreview').style.display = 'none';
    medCount = 0;
}

// Initialize nav links
document.addEventListener('DOMContentLoaded', () => {
    // Set today's date
    const today = new Date().toISOString().split('T')[0];
    const rxDate = document.getElementById('rxDate');
    if (rxDate) rxDate.value = today;

    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            navigateToPage(link.dataset.page);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.page').forEach(page => {
        if (page.id !== 'home') {
            page.style.display = 'none';
        }
    });
});