// ------------------ Global Utility ------------------
let patientIsPWD = false;
let patientDobDate = null;

function getQueryVariable(variable) {
  const query = window.location.search;
  if (!query || query.length <= 1) return false;

  const vars = query.substring(1).split("&");

  for (let i = 0; i < vars.length; i++) {
    const pair = vars[i].split("=");
    if (pair[0] === variable) {
      return pair[1];
    }
  }

  return false;
}

function toggleDesktopEditMode(editMode = true) {
  const fieldIds = [
    "fname", "mname", "lname", "suffix", "dob", "age", "pwd",
    "gender", "civstat", "cellnum", "emailadd", "addr", "hmo", "company"
  ];

  fieldIds.forEach(id => {
    $(`#desktop_${id}_span`).toggleClass("d-none", editMode);
    $(`#desktop_${id}_input`).toggleClass("d-none", !editMode);
  });

  $('#desktop_suffix_row').toggleClass('d-none', !editMode);

  // Update button
  $('#desktop_edit_btn')
    .html(editMode ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-pencil-square"></i>')
    .attr('data-bs-title', editMode ? 'Update' : 'Edit')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Cancel button
  $('#desktop_cancel_btn')
    .toggleClass('d-none', !editMode)
    .attr('data-bs-title', 'Cancel')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Delete button
  $('#desktop_delete_btn')
    .toggleClass('d-none', !editMode)
    .attr('data-bs-title', 'Delete')
    .attr('data-bs-toggle', 'tooltip')
    .removeAttr('title');

  // Re-init tooltips ONLY on visible buttons
  $('#desktop_edit_btn:visible, #desktop_cancel_btn:visible, #desktop_delete_btn:visible').each(function () {
    const existing = bootstrap.Tooltip.getInstance(this);
    if (existing) existing.dispose();
    new bootstrap.Tooltip(this);
  });
}

function showToast(message, type = 'success') {
  const toastHtml = `
    <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  `;

  let container = $('#toast-container');
  if (!container.length) {
    $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3 z-1055"></div>');
    container = $('#toast-container');
  }

  const $toast = $(toastHtml);
  container.append($toast);
  new bootstrap.Toast($toast[0]).show();
}

function getShortAgeString(birthDate) {
  const today = new Date();
  let years = today.getFullYear() - birthDate.getFullYear();
  let months = today.getMonth() - birthDate.getMonth();
  let days = today.getDate() - birthDate.getDate();

  if (days < 0) {
    months--;
    const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    days += prevMonth.getDate();
  }

  if (months < 0) {
    years--;
    months += 12;
  }

  return `${years}y ${months}m ${days}d`;
}

function loadPatientSummary(id) {
  $.ajax({
    url: 'get_latest_activity.php',
    type: 'GET',
    data: { px_id: btoa(id) },
    dataType: 'json',
    success: function (data) {
      const sections = [
        { key: 'last_medhist', label: '#summary_medhist' },
        { key: 'last_prescription', label: '#summary_prescription' },
        { key: 'last_lab', label: '#summary_lab' },
        { key: 'last_appointment', label: '#summary_appointment' }
      ];

      sections.forEach(section => {
        const value = data[section.key];
        const display = formatShortDate(value);
        const tooltip = timeAgo(value);
        $(section.label).html(
          `<span data-bs-toggle="tooltip" title="${tooltip}">${display}</span>`
        );
      });

      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const existing = bootstrap.Tooltip.getInstance(el);
        if (existing) existing.dispose();
        new bootstrap.Tooltip(el);
      });
    },
    error: function () {
      $('#summary_medhist, #summary_prescription, #summary_lab, #summary_appointment').text('Error');
    }
  });
}

function parseDateLocal(dateStr) {
  const [year, month, day] = dateStr.split('-').map(Number);
  return new Date(year, month - 1, day);
}

function formatShortDate(dateStr) {
  if (!dateStr) return 'None';
  const date = parseDateLocal(dateStr);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
}

function timeAgo(dateStr) {
  if (!dateStr) return '';
  const now = new Date();
  const target = parseDateLocal(dateStr);

  const diffMs = target - now;
  const diffMins = Math.round(diffMs / (1000 * 60));
  const diffHours = Math.round(diffMs / (1000 * 60 * 60));
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

  const absDays = Math.abs(diffDays);
  const isFuture = diffMs > 0;

  const isSameDay = now.toDateString() === target.toDateString();

  if (diffDays === 0) {
    if (isFuture) {
      if (Math.abs(diffHours) >= 1) return `In ${Math.abs(diffHours)} hour${Math.abs(diffHours) > 1 ? 's' : ''}`;
      if (Math.abs(diffMins) >= 1) return `In ${Math.abs(diffMins)} minute${Math.abs(diffMins) > 1 ? 's' : ''}`;
      return 'In a few seconds';
    } else {
      if (Math.abs(diffHours) >= 1) return `${Math.abs(diffHours)} hour${Math.abs(diffHours) > 1 ? 's' : ''} ago`;
      if (Math.abs(diffMins) >= 1) return `${Math.abs(diffMins)} minute${Math.abs(diffMins) > 1 ? 's' : ''} ago`;
      return 'Just now';
    }
  }

  if (diffDays === 1) return 'Tomorrow';
  if (diffDays === -1) return 'Yesterday';

  const absYears = Math.floor(absDays / 365);
  const absMonths = Math.floor((absDays % 365) / 30);
  const absWeeks = Math.floor((absDays % 30) / 7);

  if (!isFuture) {
    if (absYears >= 1) return `${absYears} year${absYears > 1 ? 's' : ''} ago`;
    if (absMonths >= 1) return `${absMonths} month${absMonths > 1 ? 's' : ''} ago`;
    if (absWeeks >= 1) return `${absWeeks} week${absWeeks > 1 ? 's' : ''} ago`;
    return `${absDays} day${absDays > 1 ? 's' : ''} ago`;
  } else {
    if (absYears >= 1) return `Upcoming in ${absYears} year${absYears > 1 ? 's' : ''}`;
    if (absMonths >= 1) return `Upcoming in ${absMonths} month${absMonths > 1 ? 's' : ''}`;
    if (absWeeks >= 1) return `Upcoming in ${absWeeks} week${absWeeks > 1 ? 's' : ''}`;
    return `Upcoming in ${absDays} day${absDays > 1 ? 's' : ''}`;
  }
}

const labTestNames = [
  "FBS", "BUN", "CREATININE", "URIC ACID", "CHOLESTEROL", "TRIGLYCERIDES",
  "HDL CHOLE", "LDL CHOLE", "SGOT", "SGPT", "SODIUM", "POTASSIUM", "HBA1C",
  "LDH", "ALK PHOS", "CHLORIDE", "TOTAL PROTEIN", "ALBUMIN", "GLOBULIN",
  "A/G RATIO", "TOTAL BILIRUBIN", "DIRECT BILIRUBIN", "INDIRECT BILIRUBIN",
  "CALCIUM", "GGTP", "PHOSPHORUS", "AMYLASE", "CK TOTAL", "FT3", "FT4",
  "TSH", "VITAMIN D"
];

const fuseOptions = {
  threshold: 0.3,
  ignoreLocation: true
};

function enableLabAutocomplete(textareaId, suggestionBoxId) {
  const input = document.getElementById(textareaId);
  const box = document.getElementById(suggestionBoxId);
  const fuse = new Fuse(labTestNames, fuseOptions);

  let activeIndex = -1;
  let currentSuggestions = [];

  function showSuggestions(currentLine) {
    const matches = currentLine.length > 0
      ? fuse.search(currentLine).map(r => r.item).slice(0, 6)
      : [];

    box.innerHTML = '';
    currentSuggestions = matches;
    activeIndex = -1;

    if (matches.length > 0) {
      matches.forEach((match, idx) => {
        const li = document.createElement('li');
        li.className = 'list-group-item list-group-item-action';
        li.textContent = match;
        li.dataset.index = idx;
        li.addEventListener('click', () => applySuggestion(idx));
        box.appendChild(li);
      });
      box.style.display = 'block';
    } else {
      box.style.display = 'none';
    }
  }

  function applySuggestion(index) {
    const lines = input.value.split('\n');
    lines[lines.length - 1] = currentSuggestions[index] + ': ';
    input.value = lines.join('\n');
    box.style.display = 'none';
    input.focus();
  }

  function updateActive(items) {
    items.forEach((item, idx) => {
      if (idx === activeIndex) {
        item.classList.add('active');
        item.scrollIntoView({ block: 'nearest' });
      } else {
        item.classList.remove('active');
      }
    });
  }

  input.addEventListener('input', () => {
    const lines = input.value.split('\n');
    const currentLine = lines[lines.length - 1].trim();
    const shouldSuggest = currentLine && !currentLine.includes(':');
    if (shouldSuggest) showSuggestions(currentLine);
    else box.style.display = 'none';
  });

  input.addEventListener('keydown', (e) => {
    const items = box.querySelectorAll('li');

    if (box.style.display === 'block' && items.length > 0) {
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = (activeIndex + 1) % items.length;
        updateActive(items);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = (activeIndex - 1 + items.length) % items.length;
        updateActive(items);
      } else if (e.key === 'Enter') {
        if (activeIndex >= 0 && currentSuggestions[activeIndex]) {
          e.preventDefault();
          applySuggestion(activeIndex);
        }
      }
    }
  });

  input.addEventListener('blur', () => {
    setTimeout(() => box.style.display = 'none', 100);
  });
}

function sanitizeLabInput(text, px_gender = 'Male') {
  px_gender = (px_gender || 'Male').toLowerCase();

  const refRanges = {
    FBS: [60, 99],
    BUN: [4.76, 23.25],
    CREATININE: px_gender === 'female' ? [0.5, 1.0] : [0.7, 1.2],
    'URIC ACID': [149, 405],
    CHOLESTEROL: [0, 200],
    TRIGLYCERIDES: [35, 160],
    'HDL CHOLE': [40, 60],
    'LDL CHOLE': [0, 100],
    SGOT: px_gender === 'female' ? [0, 32] : [0, 38],
    SGPT: px_gender === 'female' ? [0, 31] : [0, 41],
    SODIUM: [135, 148],
    POTASSIUM: [3.5, 5.1],
    HBA1C: [4, 5.99],
    LDH: px_gender === 'female' ? [135, 214] : [135, 225],
    'ALK PHOS': [40, 129],
    CHLORIDE: [98, 111],
    'TOTAL PROTEIN': [6.4, 8.3],
    ALBUMIN: [3.4, 4.8],
    GLOBULIN: [2, 3.5],
    'A/G RATIO': [1, 2.5],
    'TOTAL BILIRUBIN': [.1, 1.2],
    'DIRECT BILIRUBIN': [0, .3],
    'INDIRECT BILIRUBIN': [.1, 1],
    CALCIUM: [8.4, 10.2],
    GGTP: [0, 60],
    PHOSPHORUS: [2.51, 4.5],
    AMYLASE: [28, 100],
    'CK TOTAL': px_gender === 'female' ? [0, 170] : [0, 190],
    FT3: [2.02, 4.43],
    FT4: [0.93, 1.71],
    TSH: [0.27, 4.20],
    'VITAMIN D': [75, Number.POSITIVE_INFINITY],
  };

  const aliases = {
    HDL: 'HDL CHOLE',
    LDL: 'LDL CHOLE',
    CREA: 'CREATININE',
    TRIGLY: 'TRIGLYCERIDES',
    CHOLE: 'CHOLESTEROL',
    ALT: 'SGPT',
    AST: 'SGOT',
    VITD: 'VITAMIN D',
    'VIT D': 'VITAMIN D',
    URIC: 'URIC ACID',
    NA: 'SODIUM',
    K: 'POTASSIUM',
    CA: 'CALCIUM'    
  };

  function getTooltip(normalized, min, max) {
    return (normalized === 'HDL CHOLE' || normalized === 'VITAMIN D')
      ? `Normal range: ${min} and above`
      : `Normal range: ${min}–${max}`;
  }

  return text
    .split('\n')
    .map(line => {
      let [name, value] = line.split(':').map(s => s.trim());

      if (!value) {
        const parts = line.trim().split(/\s+/);
        const joined = (parts[0] + ' ' + (parts[1] || '')).toUpperCase();
        if (aliases[joined]) {
          name = joined;
          value = parts[2];
        } else {
          name = parts[0];
          value = parts[1];
        }
      }

      if (!name || !value) return '';

      const normalized = aliases[name.toUpperCase()] || name.toUpperCase();
      const numeric = parseFloat(value.replace(',', ''));
      const range = refRanges[normalized];

      if (!range || isNaN(numeric)) return `${normalized}: ${value}`;

      const [min, max] = range;
      const isMinOnly = normalized === 'HDL CHOLE' || normalized === 'VITAMIN D';
      const isOutlier = isMinOnly
        ? numeric < min
        : numeric < min || numeric > max;

      const tooltip = getTooltip(normalized, min, max);

      return isOutlier
        ? `${normalized}: <span style="color:red;" data-bs-toggle="tooltip" title="${tooltip}">${numeric}</span>`
        : `${normalized}: ${numeric}`;
    })
    .filter(Boolean)
    .join('<br>');
}

function highlightOutliers(labText, prevLabText = '', px_gender = 'Male') {
  px_gender = (px_gender || 'Male').toLowerCase();

  const refRanges = {
    FBS: [60, 99],
    BUN: [4.76, 23.25],
    CREATININE: px_gender === 'female' ? [0.5, 1.0] : [0.7, 1.2],
    'URIC ACID': [149, 405],
    CHOLESTEROL: [0, 200],
    TRIGLYCERIDES: [35, 160],
    'HDL CHOLE': [40, Number.POSITIVE_INFINITY],
    'LDL CHOLE': [0, 100],
    SGOT: px_gender === 'female' ? [0, 32] : [0, 38],
    SGPT: px_gender === 'female' ? [0, 31] : [0, 41],
    SODIUM: [135, 148],
    POTASSIUM: [3.5, 5.1],
    HBA1C: [4, 5.99],
    LDH: px_gender === 'female' ? [135, 214] : [135, 225],
    'ALK PHOS': [40, 129],
    CHLORIDE: [98, 111],
    'TOTAL PROTEIN': [6.4, 8.3],
    ALBUMIN: [3.4, 4.8],
    GLOBULIN: [2, 3.5],
    'A/G RATIO': [1, 2.5],
    'TOTAL BILIRUBIN': [0.1, 1.2],
    'DIRECT BILIRUBIN': [0, 0.3],
    'INDIRECT BILIRUBIN': [0.1, 1],
    CALCIUM: [8.4, 10.2],
    GGTP: [0, 60],
    PHOSPHORUS: [2.51, 4.5],
    AMYLASE: [28, 100],
    'CK TOTAL': px_gender === 'female' ? [0, 170] : [0, 190],
    FT3: [2.02, 4.43],
    FT4: [0.93, 1.71],
    TSH: [0.27, 4.20],
    'VITAMIN D': [75, Number.POSITIVE_INFINITY],
  };

  const aliases = {
    HDL: "HDL CHOLE",
    LDL: "LDL CHOLE",
    CREA: "CREATININE",
    TRIGLY: "TRIGLYCERIDES",
    CHOLE: "CHOLESTEROL",
    ALT: "SGPT",
    AST: "SGOT",
    VITD: "VITAMIN D",
    'VIT D': "VITAMIN D",
    URIC: "URIC ACID",
    NA: "SODIUM",
    K: "POTASSIUM",
    CA: "CALCIUM"
  };

  function parse(text) {
    const rawMap = {};
    const numMap = {};

    text.split('\n').forEach((line, index) => {
      line = line.trim();
      if (!line) return;

      if (!/\d/.test(line)) {
        rawMap[line] = null;
        return;
      }

      let key = '', val = '';
      let match = line.match(/^(.+?)\s*[:\-]\s*(.+)$/);
      if (match) {
        key = match[1].trim();
        val = match[2].trim();
      } else {
        const parts = line.split(/\s+/);
        const possibleVal = parts[parts.length - 1];
        const num = parseFloat(possibleVal.replace(',', ''));
        if (!isNaN(num)) {
          val = possibleVal;
          key = parts.slice(0, -1).join(' ');
        } else {
          rawMap[line] = null;
          return;
        }
      }

      const cleanedKey = key.toUpperCase().replace(/\s+/g, ' ').trim();
      const finalKey = refRanges[cleanedKey] ? cleanedKey : (aliases[cleanedKey] || cleanedKey);

      rawMap[finalKey] = val;
      const numeric = parseFloat(val.replace(',', ''));
      if (!isNaN(numeric)) numMap[finalKey] = numeric;
    });

    return { rawMap, numMap };
  }

  const { rawMap: current, numMap: currentNum } = parse(labText);
  const { rawMap: previous, numMap: prevNum } = parse(prevLabText);

  return Object.entries(current).map(([key, rawVal]) => {
    if (rawVal === null) return key;

    const range = refRanges[key];
    const val = currentNum[key];
    const prevVal = prevNum[key];

    if (!range || val === undefined) {
      return `${key}: ${rawVal}`;
    }

    const [min, max] = range;
    const isMinOnly = key === 'HDL CHOLE' || key === 'VITAMIN D';
    const nowNormal = isMinOnly ? val >= min : val >= min && val <= max;
    const wasNormal = prevVal === undefined ? null : (isMinOnly ? prevVal >= min : prevVal >= min && prevVal <= max);
    const tooltip = isMinOnly ? `Normal range: ${min} and above` : `Normal range: ${min}–${max}`;

    // Determine directionality
    let arrow = '';
    if (prevVal !== undefined && val !== prevVal) {
      const wasOutlier = wasNormal === false;
      const nowOutlier = nowNormal === false;
      const stillOutlier = wasOutlier && nowOutlier;
      const statusChanged = wasNormal !== nowNormal;

      const movedUp = val > prevVal;
      let movedTowardNormal = false;

      if (stillOutlier) {
        const prevDist = prevVal < min ? min - prevVal : prevVal - max;
        const nowDist = val < min ? min - val : val - max;
        movedTowardNormal = nowDist < prevDist;
      } else if (statusChanged) {
        movedTowardNormal = nowNormal === true;
      } else {
        const center = (min + max) / 2;
        movedTowardNormal = Math.abs(val - center) < Math.abs(prevVal - center);
      }

      let label = '', color = '';
      if (stillOutlier) {
        label = movedTowardNormal ? 'Improving' : 'Worsening';
        color = movedTowardNormal ? 'text-success' : 'text-danger';
      } else if (statusChanged) {
        label = movedUp ? 'Increased' : 'Decreased';
        color = movedTowardNormal ? 'text-success' : 'text-danger';
      } else {
        label = movedUp ? 'Increased' : 'Decreased';
        color = movedTowardNormal ? 'text-success' : 'text-danger';
      }

      arrow = movedUp
        ? `<i class="fas fa-arrow-up ${color}" title="${label}" data-bs-toggle="tooltip"></i>`
        : `<i class="fas fa-arrow-down ${color}" title="${label}" data-bs-toggle="tooltip"></i>`;

      arrow = `&nbsp;${arrow}`;
    }

    // Value coloring logic
    let valueHTML = '';
    if (val === undefined) {
      valueHTML = rawVal;
    } else if (!nowNormal) {
      valueHTML = `<span style="color:red;" data-bs-toggle="tooltip" title="${tooltip}">${val}</span>`;
    } else if (!isMinOnly && (val === min || val === max)) {
      valueHTML = `<span style="color:orange;" data-bs-toggle="tooltip" title="${tooltip} (borderline)">${val}</span>`;
    } else {
      valueHTML = val;
    }

    return `${key}: ${valueHTML}${arrow}`;
  }).join('<br>');
}


function updateLabResultHighlights(px_gender = 'Male') {
  const all = document.querySelectorAll('.labresult-highlight');
  console.log('Found labresult-highlight elements:', all.length);

  all.forEach((el, i) => {
    const currentRaw = el.getAttribute('data-raw') || '';
    const prevRaw = (i === 0 && all.length > 1) ? all[i + 1].getAttribute('data-raw') || '' : '';

    console.log(`Entry #${i + 1}`);
    console.log('  currentRaw:', currentRaw);
    console.log('  prevRaw:', prevRaw);

    const html = highlightOutliers(currentRaw, prevRaw, px_gender);
    console.log('  generated HTML:', html);

    el.innerHTML = html;
  });

  // ✅ Re-enable tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    const existing = bootstrap.Tooltip.getInstance(el);
    if (existing) existing.dispose();
    new bootstrap.Tooltip(el);
  });

  console.log('Tooltips reinitialized');
}

// ------------------ Patient Info Loader ------------------

function populatePatientInfo() {
  console.log("populatePatientInfo() triggered");
  const queryId = getQueryVariable('id');
  if (!queryId) {
    console.warn("No patient ID found in URL.");
    return;
  }

  $.ajax({
    type: "GET",
    url: "get_px_details.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      id: btoa(queryId)
    },
    success: function(data) {
      console.log("Raw patient data:", data);
      if (!$.isArray(data)) return;

      const px = data[0];
      const px_gender = (px.px_gender || 'Male').trim();
      window.currentPatientGender = px_gender;
      console.log('✅ Patient gender set:', window.currentPatientGender);
      document.dispatchEvent(new CustomEvent('patientInfoLoaded'));      

      // Full name
      const fullName = [px.px_firstname, px.px_midname, px.px_lastname, px.px_suffix].filter(Boolean).join(' ');
      $('#viewPx_Name').html(fullName);

      // Set modal titles
      $('#addDiagnosisLabel').html("Add Diagnosis - " + fullName);
      $('#viewDiagnosisLabel').html("View / Edit Diagnosis - " + fullName);
      $('#bookAppointmentLabel').html("Book Appointment - " + fullName);
      $('#viewAppointmentLabel').html("View / Edit Appointment - " + fullName);
      $('#addPrescriptionLabel').html("Add Prescription - " + fullName);
      $('#viewPrescriptionLabel').html("View / Edit Prescription - " + fullName);
      $('#requestLabTestLabel').html("Add Labs - " + fullName);
      $('#viewEditLabTestLabel').html("View / Edit Labs - " + fullName);

      // Age & DOB
      let formattedDob = '';
      let ageShort = '';
      let ageYears = null;
      let ageMonths = null;

      const rawDob = px.px_dob;
      let patientDobDate = null;
      if (rawDob) {
        const [year, month, day] = rawDob.split('-');
        if (year && month && day) {
          const dobDate = new Date(year, month - 1, day);
          patientDobDate = dobDate;
          const today = new Date();

          ageYears = today.getFullYear() - dobDate.getFullYear();
          ageMonths = today.getMonth() - dobDate.getMonth();

          if (ageMonths < 0 || (ageMonths === 0 && today.getDate() < dobDate.getDate())) {
            ageYears--;
            ageMonths += 12;
          }

          ageShort = `${ageYears}y ${ageMonths}m`;
          formattedDob = dobDate.toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric'
          });
        }
      }

      // Status icons
      let statusParts = [];
      if (ageYears >= 60) {
        statusParts.push(`<i class="bi bi-person-standing text-secondary" data-bs-toggle="tooltip" title="Senior"></i>`);
      }
      if (px.px_is_pwd == 1) {
        statusParts.push(`<span class="ms-1" data-bs-toggle="tooltip" title="PWD">♿</span>`);
      }
      const statusIcon = statusParts.join(' ');

      // Gender icon
      let genderHtml = '';
      if (px.px_gender === "Male") {
        genderHtml = `Male <i class="bi bi-gender-male text-primary" data-bs-toggle="tooltip" title="Male"></i>`;
      } else if (px.px_gender === "Female") {
        genderHtml = `Female <i class="bi bi-gender-female" style="color: hotpink;" data-bs-toggle="tooltip" title="Female"></i>`;
      }

      // Subtitle
      const subtitleParts = [];
      if (px.px_id) subtitleParts.push(`Patient ID: ${px.px_id}`);
      if (px.px_gender && px.px_gender !== "None") subtitleParts.push(genderHtml);
      if (formattedDob) {
        subtitleParts.push(`${formattedDob} (${ageShort}) ${statusIcon}`);
      } else if (statusIcon) {
        subtitleParts.push(statusIcon);
      }

      $('#viewPx_Subtitle').html(subtitleParts.join(' · '));

      // Set fields
      function setDesktopField(fieldId, value) {
        if (fieldId === "pwd") {
          const isPWD = value == 1 || value === true || value === "1";
          $('#desktop_pwd_span').html(isPWD ? `<span data-bs-toggle="tooltip" title="PWD">Yes ♿</span>` : 'No');
          $('#desktop_is_pwd_checkbox').prop('checked', isPWD);
        } else if (fieldId === "dob") {
          $('#desktop_dob_span').html(formattedDob || '');
          $('#desktop_dob_input').val(px.px_dob || '');
        } else if (fieldId === "age") {
          let ageDisplay = value || '';
          if (ageYears >= 60) {
            ageDisplay += ` <i class="bi bi-person-standing text-secondary" data-bs-toggle="tooltip" title="Senior"></i>`;
          }
          $('#desktop_age_span').html(ageDisplay);
          $('#desktop_age_input').val(value || '');
        } else if (fieldId === "gender") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_gender_span').html(displayVal || '');
          $('#desktop_gender_input').val(rawVal || '');
        } else if (fieldId === "hmo") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_hmo_span').html(displayVal || '');
          $('#desktop_hmo_input').val(rawVal || '');
        } else if (fieldId === "civstat") {
          const displayVal = typeof value === 'object' ? value.display : value;
          const rawVal = typeof value === 'object' ? value.raw : value;
          $('#desktop_civstat_span').html(displayVal || '');
          $('#desktop_civstat_input').val(rawVal || '');
        } else {
          $(`#desktop_${fieldId}_span`).html(value || '');
          $(`#desktop_${fieldId}_input`).val(value || '');
        }

        // Re-enable tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          const existing = bootstrap.Tooltip.getInstance(el);
          if (existing) existing.dispose();
          new bootstrap.Tooltip(el);
        });
      }

      setDesktopField("fname", px.px_firstname);
      setDesktopField("mname", px.px_midname);
      setDesktopField("lname", px.px_lastname);
      setDesktopField("suffix", px.px_suffix);
      setDesktopField("dob", formattedDob);
      setDesktopField("age", ageShort);
      setDesktopField("pwd", px.px_is_pwd);
      setDesktopField("gender", { display: genderHtml, raw: px.px_gender });
      setDesktopField("civstat", { display: px.px_civilstatus === "None" ? '' : px.px_civilstatus, raw: px.px_civilstatus });
      setDesktopField("cellnum", px.px_cellnumber);
      setDesktopField("emailadd", px.px_emailadd);
      setDesktopField("addr", px.px_address);
      setDesktopField("hmo", { display: px.px_hmo === "None" ? '' : px.px_hmo, raw: px.px_hmo });
      setDesktopField("company", px.px_company);

    
      $('#prescAge').val(ageShort);
      $('#prescSex').val(px.px_gender);
      $('#prescAddress').val(px.px_address);

      const px_id = px.px_id;
      loadPatientSummary(px_id);

      // ✅ PTR auto-toggle
      const ptrCheckbox = document.getElementById('includePtr');
      const ptrNotice = document.getElementById('ptrNotice');
      let isSenior = ageYears >= 60;
      let patientIsPWD = px.px_is_pwd == 1;

      if (isSenior || patientIsPWD) {
        ptrCheckbox.checked = true;
        ptrCheckbox.dispatchEvent(new Event('change'));

        const reasons = [];
        if (isSenior) reasons.push('Senior');
        if (patientIsPWD) reasons.push('PWD');

        ptrNotice.textContent = `PTR auto-applied for ${reasons.join(' + ')}`;
        ptrNotice.classList.remove('d-none');
      } else {
        ptrCheckbox.checked = false;
        ptrNotice.classList.add('d-none');
      }

    },
    error: function(xhr, ajaxoption, thrownerror) {
      console.log("Get Patient Details Error: " + xhr.status + " " + thrownerror);
    }
  });
}

function generateSearchableText(text) {
  const aliases = {
    hdl: "hdl chole",
    ldl: "ldl chole",
    crea: "creatinine",
    trigly: "triglycerides",
    chole: "cholesterol",
    alt: "sgpt",
    ast: "sgot",
    vitd: "vitamin d",
    "vit d": "vitamin d",
    uric: "uric acid",
    na: "sodium",
    k: "potassium",
    ca: "calcium"
  };

  let raw = text.toLowerCase();
  let expanded = raw;

  // Expand all known aliases
  for (const alias in aliases) {
    const regex = new RegExp(`\\b${alias}\\b`, "gi");
    expanded = expanded.replace(regex, aliases[alias]);
  }

  // Append aliases to the string to allow search in both directions
  let reverseAppended = expanded;

  for (const alias in aliases) {
    const full = aliases[alias];
    if (expanded.includes(full)) {
      reverseAppended += " " + alias.toLowerCase(); // ensure alias included too
    }
  }

  return reverseAppended;
}


function stripHtmlTags(html) {
  const div = document.createElement('div');
  div.innerHTML = html;
  return div.textContent || div.innerText || '';
}

// ------------------ Document Ready ------------------

$(document).ready(function(){
  let medhis_id = "";
  let apt_id = "";
  let presc_id = "";
  let labtest_id = "";
  let originalPtrValue = '';
  var today = new Date().toISOString().split('T')[0];
  // var today2 = new Date().toISOString().split('T')[0];

  // ✅ Auto-load patient info on first load (even before switching tabs)
  populatePatientInfo();
  console.log("Desktop input check:", $('#viewpx_fname').length ? "FOUND" : "NOT FOUND");

  // ✅ Load again if switching back to Patient Info tab
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr("href");
    if (target === '#nav-home') {
      populatePatientInfo();
    }
  });

let isInEditMode = false;

// ✅ DESKTOP: EDIT/UPDATE BUTTON
$('#desktop_edit_btn').on('click', function () {
  console.log("🟢 Edit/Update button clicked");

  if (!isInEditMode) {
    isInEditMode = true;
    toggleDesktopEditMode(true);
    return;
  }

  console.log("🟢 Submitting form...");

let cellnumVal = $('#desktop_cellnum_input').val().trim();
if (cellnumVal !== '' && !/^09\d{9}$/.test(cellnumVal)) {
  alert('Invalid cellphone number. It must be exactly 11 digits and start with 09.');
  return;
}

  const updatedData = {
    px_id: getQueryVariable('id'),
    fname: $('#desktop_fname_input').val(),
    mname: $('#desktop_mname_input').val(),
    lname: $('#desktop_lname_input').val(),
    suffix: $('#desktop_suffix_input').val(),
    dob: $('#desktop_dob_input').val(),
    is_pwd: $('#desktop_is_pwd_checkbox').is(':checked') ? 1 : 0,
    gender: $('#desktop_gender_input').val(),
    civstat: $('#desktop_civstat_input').val(),
    cellnum: cellnumVal, // ✅ already validated
    emailadd: $('#desktop_emailadd_input').val(),
    addr: $('#desktop_addr_input').val(),
    hmo: $('#desktop_hmo_input').val(),
    company: $('#desktop_company_input').val()
  };

  $.ajax({
    type: 'POST',
    url: 'update_px.php',
    data: updatedData,
    success: function (response) {
      console.log("Update response:", response);

      if (typeof response === 'string' && response.includes('already existing')) {
        showToast(response, 'danger');
        return;
      }

      if (typeof response === 'string' && response.includes('No patient records updated')) {
        showToast(response, 'warning');
        return;
      }

      showToast('Patient info updated successfully', 'success');
      populatePatientInfo();
      isInEditMode = false;
      toggleDesktopEditMode(false);
    },
    error: function (xhr, status, error) {
      console.error("Update failed:", xhr.status, error);
      showToast('Failed to update patient info', 'danger');
    }
  });
});


$('#desktop_delete_btn').on('click', function () {
  const pxId = getQueryVariable('id');

  if (!pxId) {
    alert("Missing patient ID.");
    return;
  }

  if (!confirm("Are you sure you want to delete this patient?")) {
    return;
  }

  $.ajax({
    type: "POST",
    url: "delete_px.php",
    data: { px_id: pxId },
    success: function (data) {
      alert(data); // ✅ Show response from PHP
      window.close(); // ✅ Close current popup
      if (window.opener && !window.opener.closed) {
        window.opener.location.reload(false); // ✅ Refresh parent
      }
    },
    error: function (xhr, status, error) {
      alert("Error deleting patient.");
      console.error("Delete error:", xhr.status, error);
    }
  });
});

$('#desktop_cancel_btn').on('click', function () {
  isInEditMode = false;
  toggleDesktopEditMode(false);
});

const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

// ------------------ Other JS & DataTable Setup ------------------

var tb_pres_history = $('#tb_pres_history').DataTable({  
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No prescription history found"
  },
  order: [],
  columnDefs: [
    {
      targets: 0,
      width: '50%',
      render: function(data, type, row) {
        if (type === 'display') {
          return data.split('\n').join('<br>');
        }
        return data;
      }
    },
    { targets: 1, width: '25%' },
    { targets: 2, width: '15%', className: 'text-center' },
    {
      targets: -1,
      data: null,
      width: '80px',
      className: 'text-center',
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_preshistory'>View</button>"
    }
  ]
});

function labResultRenderer(data, type, row) {
  if (!data) return '';
  const escapedData = escapeHtml(data); // still useful for safety
  return `<div class="labresult-highlight" data-raw="${escapedData}">${escapedData}</div>`;
}





  // Optional: escape HTML for safety
  function escapeHtml(text) {
    return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

 function bulletListRenderer(data, type, row) {
  if (type === 'display') {
    if (!data || data.trim() === "") {
      return data;
    }
    return data.split('\n').map(line => '• ' + line).join('<br>');
  }
  return data;
}

// ✅ DataTable initialization for Medical History
var tb_medhistory = $('#tb_medhistory').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No medical history found"
  },
  order: [],
  columnDefs: [
    {
      targets: 0,
      width: '20%',
      render: bulletListRenderer
    },
{
  targets: 1,
  width: '20%',
  render: {
    display: labResultRenderer,
    filter: function (data, type, row, meta) {
      const plainText = stripHtmlTags(data);
      return generateSearchableText(plainText);
    }
  }
},
    {
      targets: 2,
      width: '20%',
      render: bulletListRenderer
    },
    {
      targets: 3,
      width: '15%'
    },
    {
      targets: 4,
      width: '15%'
    },
    {
      targets: -1,
      width: '80px',
      className: 'text-center',
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_medhistory'>View</button>"
    }
  ]
});

$('#tb_medhistory').on('draw.dt', function () {
document.querySelectorAll('.labresult-highlight').forEach((el, i, all) => {
  const currentRaw = el.getAttribute('data-raw');
  const prevRaw = all[i - 1]?.getAttribute('data-raw') || '';
  const html = highlightOutliers(currentRaw, prevRaw, px_gender, i === 0); // show arrow only on latest
  el.innerHTML = html;
});

// Re-initialize tooltips
setTimeout(() => {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    const tip = bootstrap.Tooltip.getInstance(el);
    if (tip) tip.dispose();
    new bootstrap.Tooltip(el);
  });
}, 50);

});



     var tb_apt_history = $('#tb_apt_history').DataTable({
    responsive: true,
    autoWidth: false,
        "language": {
          "emptyTable": "No appointment history found"
        },
        "order": [],
        columnDefs: [
          {
            data: null,
            defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_apt_history'>View</button>",
            targets: -1,
            width: '80px',
            className: 'text-center'
          }
        ]
     });

var tb_lab = $('#tb_lab').DataTable({
  responsive: true,
  autoWidth: false,
  language: {
    emptyTable: "No lab test history found"
  },
  order: [],
  columnDefs: [
    {
      targets: -1,
      data: null,
      defaultContent: "<button type='button' class='btn btn-outline-primary btn-sm view_labtest_history'>View</button>",
      width: '80px',
      className: 'text-center'
    },
    {
      targets: 0,
      width: '45%'
    },
    {
      targets: 1,
      width: '30%'
    },
    {
      targets: 2,
      width: '15%',
      className: 'text-center'
    }
  ]
});

$('#tb_pxlist').on('click', '.view-px-btn', function () {
  const px_id = $(this).closest('tr').attr('id');

  // Redirect or open modal using px_id
  window.location.href = 'view_px.php?id=' + btoa(px_id);
});
  

    $('#labtest_request_date').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    
    $('#labtest_folldate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });

    $('#edit_labtest_folldate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });    

    $('#edit_labtest_request_date').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    
    $('#viewpx_dob').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });

    $('#diagnosisDate').datepicker({
        uiLibrary: 'bootstrap5',
        format: 'yyyy-mm-dd'
    });
    $('#issuanceDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });
    $('#viewDiagnosisDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });
  $('#viewIssuanceDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd'
});

    $('#appointmentDate').datepicker({
        uiLibrary: 'bootstrap5',
        footer: true, 
        modal: true
    });

    $('#appointmentStart').timepicker({
      uiLibrary: 'bootstrap5',
      footer: true,
      format: "hh:MM TT", 
      modal: true
  });
  $('#prescDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });

  $('#view_prescDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
  $("#prescDate, #labtest_request_date, #diagnosisDate, #issuanceDate").val(today);
  $('#prescFollDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
  $('#view_prescFollDate').datepicker({
    uiLibrary: 'bootstrap5',
    format: 'yyyy-mm-dd',
    footer: true, 
    modal: true
  });
    $('#appointmentEnd').timepicker({
        uiLibrary: 'bootstrap5',
        footer: true, 
        format: "hh:MM TT",
        modal: true
    });

    $('#viewAppointmentDate').datepicker({
      uiLibrary: 'bootstrap5',
      format: 'yyyy-mm-dd'
  });

  $('#viewAppointmentStart').timepicker({
    uiLibrary: 'bootstrap5',
        footer: true,
        format: "hh:MM TT", 
        modal: true
  });
  $('#viewAppointmentEnd').timepicker({
    uiLibrary: 'bootstrap5',
    footer: true,
    format: "hh:MM TT", 
    modal: true
  });

//Appointment History View button//
    $(document).on('click', '.view_apt_history', function () {
  apt_id = $(this).closest('tr, .card').attr('id');

  $('#delete_appointment').data('id', apt_id);

  $.ajax({
    type: "GET",
    url: "get_appointment.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      apt_id: btoa(apt_id)
    },
    success: function(data) {
      if ($.isArray(data)) {
        $('#viewAppointmentDate').val(data[0].apt_date);
        $('#viewAppointmentStart').val(data[0].apt_start);
        $('#viewAppointmentEnd').val(data[0].apt_end);
        $('#viewReason').val(data[0].apt_reason);
        $('#viewAppointment').modal('show');
      }
    },
    error: function(xhr, ajaxoption, thrownerror) {
      console.log("Get Appointment History Error: " + xhr.status + " " + thrownerror);
    }
  });
});

  // Handle click to open prescription view modal
  $(document).on('click', '.view_preshistory', function () {
  const id = $(this).closest('tr, .card').attr('id');
  presc_id = id;
  console.log("Clicked View for ID:", id);

    // Reset form and show modal early to avoid flicker
    $('#viewPrescriptionForm')[0].reset();
    $('#viewPrescription').modal('show');

    $.ajax({
      type: "GET",
      url: "get_prescription.php",
      dataType: "json",
      data: { presc_id: btoa(id) },
      success: function (data) {
        if (Array.isArray(data) && data.length > 0) {
          const presc = data[0];
          console.log("DOB received:", presc.px_dob);
          const isRegulated = parseInt(presc.regulated) === 1;
          originalPtrValue = presc.ptr || '';

          // Populate fields
          $('#view_prescDate').val(presc.presc_date).prop('readonly', false);
          $('#view_prescription').val(presc.presc).prop('readonly', false);
          $('#view_prescFollDate').val(presc.presc_folldate).prop('readonly', false);
          $('#view_prescAddress').val(presc.px_address || '').prop('disabled', true);
          $('#view_prescSex').val(presc.px_gender || '');
          $('#view_prescAge').val(presc.px_age || '');
          $('#dl_tag').attr('href', presc.fl);
          $('#view_prescPTR').val(originalPtrValue).prop('readonly', true);

          // Regulated logic
          if (isRegulated) {
            $('#view_includePtrToggle').addClass('d-none');
            $('#view_prescPTR').removeClass('d-none').show();
            $('#view_s2Wrapper').removeClass('d-none').show();
            $('#view_prescS2').val(presc.s2 || '').prop('readonly', true);
          } else {
            $('#view_includePtrToggle').removeClass('d-none').show();
            $('#view_ptrWrapper').removeClass('d-none').show();

            if (originalPtrValue !== '') {
            $('#view_includePtr').prop('checked', true);
            $('#view_prescPTR').removeClass('d-none').show().val(originalPtrValue);
          } else {
            $('#view_includePtr').prop('checked', false);
            $('#view_prescPTR').addClass('d-none').hide().val('');
          }

            $('#view_s2Wrapper').addClass('d-none').hide();
            $('#view_prescS2').val('');
          }

          // Regulated toggle status
          $('#view_isRegulated').prop('checked', isRegulated).prop('disabled', true);
          $('#view_regulatedStatus')
            .text(isRegulated ? 'YES' : '')
            .toggleClass('text-success', isRegulated)
            .toggleClass('text-danger', !isRegulated);
          $('#view_isRegulated_hidden').val(isRegulated ? '1' : '0');

          // 🔽 Insert this block right after
          patientDobDate = presc.px_dob || null;
          if (!patientDobDate || patientDobDate === 'null' || patientDobDate === '') {
            $('#regen_prescription_pdf').hide();
          } else {
            $('#regen_prescription_pdf').show();
          }

        }
      },
      error: function () {
        alert("Error fetching prescription data.");
      }
    });
  });

  // Handle PTR checkbox toggle for non-regulated drugs
  $('#view_includePtr').on('change', function () {
  const ptrInput = $('#view_prescPTR');

  if (this.checked) {
    ptrInput.removeClass('d-none').show().prop('readonly', true);
    
    // If PTR is blank, try fetching the default one
    if (!ptrInput.val().trim()) {
      fetch('fetch_ptr_s2.php')
        .then(res => res.json())
        .then(data => {
          const ptr = data.ptr || '';
          ptrInput.val(ptr);
        });
    }
  } else {
    ptrInput.addClass('d-none').hide().val('');
  }
});

  // Reset modal on close
  $('#viewPrescription').on('hidden.bs.modal', () => {
  $('#viewPrescriptionForm')[0].reset();

  $('#view_ptrWrapper').removeClass('d-none').show();        // always show wrapper
  $('#view_prescPTR').addClass('d-none').hide().val('');
  $('#view_includePtrToggle').removeClass('d-none').show();  // always show toggle
  $('#view_includePtr').prop('checked', false);
  $('#view_s2Wrapper').addClass('d-none').hide();
});

// Labtest button (consistent with others)
$(document).on('click', '.view_labtest_history', function () {
  labtest_id = $(this).closest('tr, .card').attr('id');

  $('#viewEditLabTestModal').modal('show');

  $.ajax({
    type: "GET",
    url: "get_labtest.php",
    dataType: "json",
    data: { labtest_id: btoa(labtest_id) },
    success: function (data) {
      if ($.isArray(data) && data.length > 0) {
        const lab = data[0];

        $('#labtest_id').val(lab.labtest_id);
        $('#edit_otherTests').val(lab.labtest_others);
        $('#edit_labtest_clinical_impression').val(lab.labtest_clinical_impression);
        $('#edit_labtest_remarks').val(lab.labtest_remarks);
        $('#edit_labtest_request_date').val(lab.labtest_request_date);
        $('#edit_labtest_folldate').val(lab.labtest_folldate);
        $('#dload_tag').attr('href', lab.pdf_filename);

        $('#viewEditLabTestForm input[type="checkbox"]').prop('checked', false);

        if (lab.labtest_selected) {
          const selectedTests = lab.labtest_selected
            .split(',')
            .map(test => test.trim().toLowerCase());

          $('#viewEditLabTestForm input[type="checkbox"]').each(function () {
            if (selectedTests.includes($(this).val().trim().toLowerCase())) {
              $(this).prop('checked', true);
            }
          });
        }
      }
    },
    error: function (xhr, status, error) {
      console.error("Lab test fetch failed:", xhr.status, error);
    }
  });
});

// Med History View button
$(document).on('click', '.view_medhistory', function () {
  medhis_id = $(this).closest('tr, .card').attr('id');

  $.ajax({
    type: "GET",
    url: "get_diagnosis.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    dataType: "json",
    data: {
      medhis_id: btoa(medhis_id)
    },
    success: function (data) {
      if ($.isArray(data)) {
        const result = data[0];
        $('#viewLabResult').val(result.medhis_labresult);
        // Highlight with patient gender:
        $('#labResultHighlight')
          .attr('data-raw', result.medhis_labresult)
          .html(highlightOutliers(result.medhis_labresult, window.currentPatientGender));  
        // Re-init tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          const existing = bootstrap.Tooltip.getInstance(el);
          if (existing) existing.dispose();
          new bootstrap.Tooltip(el);
        });
        $('#viewDiag').val(data[0].medhis_diagnosis);
        $('#viewMedication').val(data[0].medhis_medication);
        $('#viewRecom').val(data[0].medhis_recommendation);
        $('#viewDiagnosisDate').val(data[0].medhis_diagnosis_date);
        $('#viewIssuanceDate').val(data[0].medhis_issuance_date);
        $('#download_tag').attr('href', data[0].medhis_attachment);

        // Check if Diagnosis and Recommendation are non-empty before showing modal
        const diag = (data[0].medhis_diagnosis || '').trim();
        const recom = (data[0].medhis_recommendation || '').trim();
        const readyToEmail = diag !== '' && recom !== '';
        $('#generate_medcert').toggle(readyToEmail);

        $('#viewDiagnosis').modal('show');
      }
    },
    error: function (xhr, ajaxoption, thrownerror) {
      console.log("Get Table Medical History Error: " + xhr.status + " " + thrownerror);
    }
  });
});

     $('#update_prescription').click(function () {
  // Temporarily enable all disabled fields in the form
  const $form = $('#viewPrescriptionForm');
  const disabledFields = $form.find(':input:disabled').removeAttr('disabled');

  // Serialize the form after enabling
  const formData = $form.serialize();
  const fullData = formData + '&presc_id=' + encodeURIComponent(presc_id);

  // Re-disable only specific fields if necessary (like address, S2, etc.)
  $('#view_prescAddress').prop('disabled', true);
  $('#view_isRegulated').prop('disabled', true);

  $.ajax({
    type: 'POST',
    url: 'update_prescription.php',
    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    dataType: 'text',
    data: fullData,
    cache: false,
    success: function (data) {
      alert(data);
      location.reload();
    },
    error: function (xhr, ajaxoption, thrownerror) {
      alert(xhr.status + " " + thrownerror);
    }
  });
});

// When 'Regenerate PDF' button is clicked
$('#regen_prescription_pdf').click(function () {
  if (!presc_id) {
    alert("Missing prescription ID.");
    return;
  }

  // Disable the button temporarily
  $('#regen_prescription_pdf').prop('disabled', true).text('Regenerating...');

  $.ajax({
    type: 'POST',
    url: 'regen_prescription_pdf.php',
    data: { presc_id },
    success: function (res) {
      alert(res); // Should say: PDF regenerated with new filename

      // Refresh the prescription data to get new file path
      $.get('get_prescription.php', { presc_id: btoa(presc_id) }, function (data) {
        if (Array.isArray(data) && data.length > 0) {
          const updated = data[0];
          const newFile = updated.fl || '';
          
          if (newFile) {
            // Update the download link and force cache refresh
            $('#dl_tag').attr('href', newFile + '?v=' + Date.now());
          }
        }

        $('#regen_prescription_pdf').prop('disabled', false).text('Regenerate PDF');
      }, 'json');
    },
    error: function (xhr) {
      alert("Error: " + xhr.status + " - " + xhr.statusText);
      $('#regen_prescription_pdf').prop('disabled', false).text('Regenerate PDF');
    }
  });
});


$('#delete_prescription').click(function(){
      if (confirm("Are you sure you want to delete this prescription?"))
        {
          $('#loadingModal').modal('show');
          $.ajax({
            type: "POST",
            url: "delete_prescription.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data: {
              id: encodeURIComponent(presc_id)
            },
            success: function(data)
            {
              alert(data)
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          })
        }
     });


$('#generate_prescription').click(function () {
  Swal.fire({
    title: 'Are you sure?',
    text: "Are you sure you want to send this prescription to the patient's email?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Sending Email...',
      html: 'Please wait while we send the prescription to the patient.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.ajax({
      type: "POST",
      url: "generate_prescription.php",
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      cache: false,
      data: {
        id: encodeURIComponent(presc_id)
      },
      success: function (data) {
        Swal.fire({
          icon: 'success',
          title: 'Email Sent',
          text: data
        }).then(() => location.reload());
      },
      error: function (xhr, ajaxoption, thrownerror) {
        Swal.fire({
          icon: 'error',
          title: 'Email Not Sent',
          text: xhr.responseText || (xhr.status + " " + thrownerror)
        });
      }
    });
  });
});
    

$('#generate_medcert').click(function () {
  var diagnosis = $('#viewDiag').val().trim();
  var recommendation = $('#viewRecom').val().trim();

  // if (diagnosis === '' || recommendation === '') {
  //   Swal.fire({
  //     icon: 'warning',
  //     title: 'Missing Information',
  //     text: 'Please fill out both the Diagnosis and Recommendation fields before sending the medical certificate.',
  //   });
  //   return;
  // }

  Swal.fire({
    title: 'Are you sure?',
    text: "Are you sure you want to send this medical certificate to the patient's email?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Sending Email...',
      html: 'Please wait while we send the medical certificate to the patient.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.ajax({
      type: "POST",
      url: "generate_medcert.php",
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      cache: false,
      data: {
        medhis_id: encodeURIComponent(medhis_id)
      },
      success: function (data) {
        Swal.fire({
          icon: 'success',
          title: 'Email Sent',
          text: data
        }).then(() => location.reload());
      },
      error: function (xhr, ajaxoption, thrownerror) {
        Swal.fire({
          icon: 'error',
          title: 'Email Not Sent',
          text: xhr.responseText || (xhr.status + " " + thrownerror)
        });
      }
    });
  });
});



$('#generate_labtest').click(function () {
  Swal.fire({
    title: 'Are you sure?',
    text: "Are you sure you want to send this to the patient's email?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Sending Email...',
      html: 'Please wait while we send the lab request to the patient.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.ajax({
      type: "POST",
      url: "generate_labtest.php",
      contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
      cache: false,
      data: {
        labtest_id: encodeURIComponent(labtest_id)
      },
      success: function (data) {
        Swal.fire({
          icon: 'success',
          title: 'Email Sent',
          text: data
        }).then(() => location.reload());
      },
      error: function (xhr, ajaxoption, thrownerror) {
        Swal.fire({
          icon: 'error',
          title: 'Email Not Sent',
          text: xhr.responseText || (xhr.status + " " + thrownerror)
        });
      }
    });
  });
});
     

     $('#delete_diagnosis').click(function(){
      if (confirm("Are you sure you want to delete this diagnosis?"))
        {
          $.ajax({
            type: "POST",
            url: "delete_diagnosis.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              medhis_id: encodeURIComponent(medhis_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });
     
$('#delete_labtest').click(function(){
      if (confirm("Are you sure you want to delete this labtest?"))
        {
          $.ajax({
            type: "POST",
            url: "delete_labtest.php",
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            cache: false,
            data:{
              labtest_id: encodeURIComponent(labtest_id)
            },
            success: function(data)
            {
              alert(data);
              location.reload();
            },
            error: function(xhr, ajaxoption, thrownerror)
            {
              console.log(xhr.status+" "+thrownerror)
            }
          });
        }
     });

$('#delete_appointment').click(function () {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This appointment will be permanently deleted.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete it',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: 'Deleting...',
        html: 'Please wait while we delete the appointment.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });

      $.ajax({
        type: "POST",
        url: "delete_appointment.php",
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        cache: false,
        data: {
          apt_id: encodeURIComponent(apt_id)
        },
success: function (data) {
  Swal.close();
  const trimmed = data.trim().toLowerCase();

  let icon = 'success';
  let title = 'Appointment Deleted';
  let message = data;

  if (trimmed.includes("no email address")) {
    message = "Email not sent. No email address on file.";
  } else if (trimmed.includes("email has been sent")) {
    message = "Confirmation email sent to the patient.";
  } else if (
    trimmed.includes("error") ||
    trimmed.includes("failed")
  ) {
    icon = 'error';
    title = 'Delete Failed';
  }

  Swal.fire({
    icon,
    title,
    text: message
  }).then(() => {
    if (icon === 'success') location.reload();
  });
},
        error: function (xhr, ajaxoption, thrownerror) {
          Swal.close();
          Swal.fire({
            icon: 'error',
            title: 'Request Failed',
            text: xhr.status + " " + thrownerror
          });
        }
      });
    }
  });
});


     $('#update_diagnosis').click(function(){
      var labresult = $('#viewLabResult').val();
      var diagnosis = $('#viewDiag').val();
      var medication = $('#viewMedication').val();
      var recommendation = $('#viewRecom').val();
      var diagdate = $('#viewDiagnosisDate').val();
      var issued_date = $('#viewIssuanceDate').val();
      $.ajax({
        type: "POST",
        url: "update_diagnosis.php",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        cache: false,
        data: {
          medhis_id: encodeURIComponent(medhis_id),
          labresult: encodeURIComponent(labresult),
          diagnosis: encodeURIComponent(diagnosis),
          medication: encodeURIComponent(medication),
          recommendation: encodeURIComponent(recommendation),
          diagdate: encodeURIComponent(diagdate),
          issuancedate: encodeURIComponent(issued_date)
        },
        success: function(data)
        {
          alert(data)
          location.reload();
        },
        error: function(xhr, ajaxoption, thrownerror)
        {
          console.log(xhr.status+" "+thrownerror)
        }
      })
    });

    $('#update_labtest').click(function () {
  var labtest_request_date = $('#edit_labtest_request_date').val().trim();
  var labtest_clinical_impression = $('#edit_labtest_clinical_impression').val().trim();
  var labtest_remarks = $('#edit_labtest_remarks').val().trim();
  var labtest_folldate = $('#edit_labtest_folldate').val().trim();
  var labtest_others = $('#edit_otherTests').val().trim();

  // Collect checked lab tests
  var selectedTests = [];
  $("input[name='lab_tests[]']:checked").each(function () {
    selectedTests.push($(this).val());
  });

  $('#update_labtest').prop('disabled', true); // prevent double click

  $.ajax({
    type: "POST",
    url: "update_labtest.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    data: {
      labtest_id: labtest_id, // uses globally declared variable
      edit_labtest_request_date: labtest_request_date,
      edit_labtest_clinical_impression: labtest_clinical_impression,
      edit_labtest_remarks: labtest_remarks,
      edit_labtest_folldate: labtest_folldate,
      labtest_others: labtest_others,
      'lab_tests[]': selectedTests // no need to encode this array
    },
    success: function (response) {
      alert(response);
      location.reload();
    },
    error: function (xhr, ajaxOptions, thrownError) {
      console.log(xhr.status + " " + thrownError);
    }
  });
});

$('#update_appointment').click(function () {
  const apt_date = $('#viewAppointmentDate').val();
  const apt_start = $('#viewAppointmentStart').val();
  const apt_end = $('#viewAppointmentEnd').val();
  const apt_reason = $('#viewReason').val();

  if (!apt_date || !apt_start || !apt_end || !apt_reason.trim()) {
    Swal.fire({
      icon: 'warning',
      title: 'Missing Information',
      text: 'Please complete all appointment fields.'
    });
    return;
  }

  Swal.fire({
    title: 'Updating Appointment...',
    html: 'Please wait while we save the changes and send the confirmation email.',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    type: "POST",
    url: "update_appointment.php",
    contentType: "application/x-www-form-urlencoded; charset=UTF-8",
    cache: false,
    data: {
      apt_date: apt_date,
      apt_start: apt_start,
      apt_end: apt_end,
      apt_reason: encodeURIComponent(apt_reason),
      apt_id: encodeURIComponent(apt_id)
    },
    success: function (data) {
      Swal.close();
      const trimmed = data.trim().toLowerCase();

      let icon = 'success';
      let title = 'Appointment Updated';
      let message = data;

      if (trimmed.includes("no email address")) {
        message = "Email not sent. No email address on file.";
      } else if (trimmed.includes("email has been sent")) {
        message = "Confirmation email sent to the patient.";
      } else if (
        trimmed.includes("invalid time") ||
        trimmed.includes("no changes")
      ) {
        icon = 'error';
        title = 'Update Failed';
      }

      Swal.fire({
        icon,
        title,
        text: message
      }).then(() => {
        if (icon === 'success') location.reload();
      });
    },
    error: function (xhr, ajaxoption, thrownerror) {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Request Failed',
        text: xhr.status + " " + thrownerror
      });
    }
  });
});


$('#generate_appointment').click(function () {
  Swal.fire({
    title: 'Are you sure?',
    text: "Are you sure you want to send this booking to the patient's email?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Sending Email...',
      html: 'Please wait while we send the appointment details to the patient.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    $.ajax({
      type: "POST",
      url: "generate_appointment.php",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      data: {
        apt_id: encodeURIComponent(apt_id)
      },
      success: function(data) {
        const normalized = data.trim().toLowerCase();
        const isNotSent = 
          normalized.includes("no email") || 
          normalized.includes("not sent") || 
          normalized.includes("failed");

        $('#viewAppointment').modal('hide'); // 🔻 close the modal

        Swal.fire({
          icon: isNotSent ? 'error' : 'success',
          title: isNotSent ? 'Email Not Sent' : 'Email Sent',
          text: data
        });
      },
      error: function (xhr, ajaxoption, thrownerror) {
        $('#viewAppointment').modal('hide'); // 🔻 close the modal

        Swal.fire({
          icon: 'error',
          title: 'Email Not Sent',
          text: xhr.responseText || (xhr.status + " " + thrownerror)
        });
      }
    });
  });
});



});

document.addEventListener('DOMContentLoaded', () => {
  // updateLabResultHighlights(window.currentPatientGender || 'Male');
  const apiKey = 'AIzaSyDti7FzMheLCS41r6n0JT90Y26T6cl3bwo'; // Replace with your actual API key

  // 🔍 Extract lab values from OCR text using known reference ranges
    function extractLabValues(text) {
  const refMap = [
    { name: "FBS", aliases: ["FBS"] },
    { name: "BUN", aliases: ["BUN"] },
    { name: "CREATININE", aliases: ["CREATININE"] },
    { name: "URIC ACID", aliases: ["URIC ACID"] },
    { name: "CHOLESTEROL", aliases: ["CHOLESTEROL"] },
    { name: "TRIGLYCERIDES", aliases: ["TRIGLYCERIDES"] },
    { name: "HDL CHOLE", aliases: ["HDL CHOLE", "HDL-CHOLE"] },
    { name: "LDL CHOLE", aliases: ["LDL CHOLE", "LDL-CHOLE"] },
    { name: "SGOT", aliases: ["SGOT"] },
    { name: "SGPT", aliases: ["SGPT"] },
    { name: "SODIUM", aliases: ["SODIUM"] },
    { name: "POTASSIUM", aliases: ["POTASSIUM"] },
    { name: "HBA1C", aliases: ["HBA1C", "HB A1C"] },
    { name: "LDH", aliases: ["LDH"] },
    { name: "ALK PHOS", aliases: ["ALK PHOS"] },
    { name: "CHLORIDE", aliases: ["CHLORIDE"] },
    { name: "TOTAL PROTEIN", aliases: ["TOTAL PROTEIN"] },
    { name: "ALBUMIN", aliases: ["ALBUMIN"] },
    { name: "GLOBULIN", aliases: ["GLOBULIN"] },
    { name: "A/G RATIO", aliases: ["A/G RATIO"] },
    { name: "TOTAL BILIRUBIN", aliases: ["TOTAL BILIRUBIN"] },
    { name: "DIRECT BILIRUBIN", aliases: ["DIRECT BILIRUBIN"] },
    { name: "INDIRECT BILIRUBIN", aliases: ["INDIRECT BILIRUBIN"] },
    { name: "CALCIUM", aliases: ["CALCIUM"] },
    { name: "GGTP", aliases: ["GGTP"] },
    { name: "PHOSPHORUS", aliases: ["PHOSPHORUS"] },
    { name: "AMYLASE", aliases: ["AMYLASE"] },
    { name: "CK TOTAL", aliases: ["CK TOTAL"] },
    { name: "FT3", aliases: ["FT3"] },
    { name: "FT4", aliases: ["FT4"] },
    { name: "TSH", aliases: ["TSH"] },
    { name: "VITAMIN D", aliases: ["VITAMIN D"] }
  ];

  const lines = text.split('\n').map(line => line.trim()).filter(Boolean);
  const results = [];
  const alreadyFound = new Set();

  function normalize(str) {
    return str.toUpperCase().replace(/[^A-Z0-9]/g, '');
  }

  for (let i = 0; i < lines.length; i++) {
    const currentLine = lines[i];
    const normLine = normalize(currentLine);

    for (const ref of refMap) {
      if (alreadyFound.has(ref.name)) continue;

      const matched = ref.aliases.some(alias => normLine.includes(normalize(alias)));
      if (!matched) continue;

      let value = null;
      
// 🧠 Special case for URIC ACID: always pick 2 rows above (SI unit)
if (ref.name === "URIC ACID") {
  const siLine = lines[i - 2];
  const match = siLine?.match(/[\d.]+/);
  if (match) {
    value = match[0];
  }
}

if (value) {
  results.push(`${ref.name}: ${value}`);
  alreadyFound.add(ref.name);
  continue; // prevent fallback logic from overriding
}


      // FT3 / FT4 / TSH → value is one line below
      if (["FT3", "FT4", "TSH"].includes(ref.name)) {
        for (let j = 1; j <= 2; j++) {
          const nextLine = lines[i + j];
          if (nextLine && /^\s*[\d.]+\s*$/.test(nextLine)) {
            value = nextLine.trim();
            break;
          }
        }
      }

      // VITAMIN D → value is 2–3 lines above, skip non-numeric lines
      else if (ref.name === "VITAMIN D") {
        for (let j = 1; j <= 3; j++) {
          const above = lines[i - j];
          if (above && /^\s*[A-Z]*\s*([\d.]+)\s*$/.test(above)) {
            const match = above.match(/([\d.]+)/);
            if (match) {
              value = match[1];
              break;
            }
          }
        }
      }

      // Default: Look 1–2 lines above for value
      else {
        for (let j = 1; j <= 2; j++) {
          const prevLine = lines[i - j];
          if (prevLine && /^\s*[\d.]+\s*$/.test(prevLine)) {
            value = prevLine.trim();
            break;
          }
        }
      }

      if (value) {
        results.push(`${ref.name}: ${value}`);
        alreadyFound.add(ref.name);
      }
    }
  }

  return results.length ? results.join('\n') : '❌ No valid results found.';
}

  // document.querySelectorAll('.labresult-highlight').forEach(el => {
  //   const raw = el.getAttribute('data-raw');
  //   if (raw) {
  //     el.innerHTML = highlightOutliers(raw, window.currentPatientGender);
  //   }
  // }); 

  // 📤 Image upload trigger
  document.getElementById('uploadLabImage')?.addEventListener('click', () => {
    document.getElementById('uploadInput')?.click();
  });

  // Common handler for both inputs
  ['uploadInput'].forEach(id => {
    const input = document.getElementById(id);
    if (!input) return;

    input.addEventListener('change', async function () {
      const files = Array.from(this.files);
      if (!files.length) return;

      for (const file of files) {
        const reader = new FileReader();

        reader.onloadend = async function () {
          const base64Image = reader.result.replace(/^data:image\/(png|jpeg);base64,/, '');

          const requestBody = {
            requests: [
              {
                image: { content: base64Image },
                features: [{ type: 'TEXT_DETECTION' }]
              }
            ]
          };

          try {
            const response = await fetch(`https://vision.googleapis.com/v1/images:annotate?key=${apiKey}`, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(requestBody)
            });

            const result = await response.json();
            const text = result.responses?.[0]?.fullTextAnnotation?.text || '❌ OCR failed.';

            // Append each result
            const current = document.getElementById('ocrOutput').value;
            document.getElementById('ocrOutput').value = current + '\n\n' + text;

            // Optional: Extract and merge
            const extracted = extractLabValues(text);
            const existing = document.getElementById('labresult').value;
            document.getElementById('labresult').value = existing
              ? existing.trim() + '\n' + extracted.trim()
              : extracted.trim();

          } catch (err) {
            console.error('Vision API error:', err);
          }
        };

        reader.readAsDataURL(file);
      }
    });

  });

  // --- Add Prescription Modal (Vanilla JS) ---
  const isRegulatedToggle = document.getElementById('isRegulated');
  if (!isRegulatedToggle) return;

  const regulatedStatus = document.getElementById('regulatedStatus');
  const ptrField = document.getElementById('prescPTR');
  const s2Field = document.getElementById('prescS2');
  const includePtr = document.getElementById('includePtr');
  const includePtrToggle = document.getElementById('includePtrToggle');
  const s2Wrapper = document.getElementById('s2Wrapper');

  let savedPTR = '';
  let savedS2 = '';

  function forceRepaint(el) {
    el.style.display = 'none';
    el.offsetHeight;
    el.style.display = '';
  }

  function updateFields() {
    const isRegulated = isRegulatedToggle.checked;
    const includePtrChecked = includePtr.checked;

    regulatedStatus.textContent = isRegulated ? 'YES' : '';

    if (isRegulated) {
      ptrField.classList.remove('d-none');
      ptrField.readOnly = true;
      forceRepaint(ptrField);
      if (ptrField.value.trim() === '') ptrField.value = savedPTR;

      s2Wrapper.classList.remove('d-none');
      s2Field.readOnly = true;
      forceRepaint(s2Field);
      if (s2Field.value.trim() === '') s2Field.value = savedS2;

      includePtrToggle.style.display = 'none';
    } else {
      s2Wrapper.classList.add('d-none');
      s2Field.readOnly = false;
      s2Field.value = '';

      includePtrToggle.style.display = 'block';

      if (includePtrChecked) {
        ptrField.classList.remove('d-none');
        ptrField.readOnly = true;
        forceRepaint(ptrField);
        if (ptrField.value.trim() === '') ptrField.value = savedPTR;
      } else {
        ptrField.classList.add('d-none');
        ptrField.readOnly = false;
        forceRepaint(ptrField);
        ptrField.value = '';
      }
    }
  }

  isRegulatedToggle.addEventListener('change', updateFields);
  includePtr.addEventListener('change', updateFields);

  updateFields();

  // ✅ Add Prescription Modal logic
  document.getElementById('addPrescription').addEventListener('shown.bs.modal', () => {
    // Fetch PTR and S2
    fetch('fetch_ptr_s2.php')
      .then(res => res.json())
      .then(data => {
        savedPTR = data.ptr || '';
        savedS2 = data.s2 || '';
        if (ptrField.value.trim() === '') ptrField.value = savedPTR;
        if (s2Field.value.trim() === '') s2Field.value = savedS2;
      })
      .catch(() => {
        savedPTR = '';
        savedS2 = '';
      });
  });

  // Manual input, highlights outliers //
  document.getElementById('submit_diagnosis')?.addEventListener('click', () => {
    const raw = document.getElementById('labresult').value.trim();
    const gender = window.currentPatientGender || 'Male';
    const preview = document.getElementById('viewLabResult');

    // ✅ Step 1: standardize aliases and format
    const sanitized = sanitizeLabInput(raw, gender);

    // ✅ Step 2: apply outlier highlights using gender-specific ranges
    const cleaned = highlightOutliers(sanitized, gender);

    if (preview) {
      preview.setAttribute('data-raw', sanitized);
      preview.innerHTML = cleaned;

      // ✅ Re-enable tooltips
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        const instance = bootstrap.Tooltip.getInstance(el);
        if (instance) instance.dispose();
        new bootstrap.Tooltip(el);
      });
    }
  });

  document.getElementById('includePtr').addEventListener('change', function () {
  const ptrNotice = document.getElementById('ptrNotice');
  if (this.checked) {
    ptrNotice.classList.remove('d-none');
  } else {
    ptrNotice.classList.add('d-none');
  }
  });
  });

  document.addEventListener('patientInfoLoaded', () => {
  // Patient gender is now guaranteed to be set
  updateLabResultHighlights(window.currentPatientGender);

  enableLabAutocomplete('labresult', 'autocompleteBox');
  enableLabAutocomplete('viewLabResult', 'viewautocompleteBox');

});