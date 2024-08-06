// TinyMCE initialization
tinymce.init({
  selector: 'textarea',
  plugins: [
    'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
    'searchreplace', 'table', 'visualblocks', 'wordcount', 'checklist', 'mediaembed', 'casechange',
    'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen',
    'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'mentions', 'tinycomments',
    'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown'
  ],
  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
  tinycomments_mode: 'embedded',
  tinycomments_author: 'Author name',
  mergetags_list: [
    { value: 'First.Name', title: 'First Name' },
    { value: 'Email', title: 'Email' },
  ],
});

// Toggle section functionality
function toggleSection(sectionId) {
  const sectionContent = document.querySelector(`#${sectionId} .section-content`);
  const toggleIcon = document.querySelector(`#${sectionId} ion-icon`);

  if (sectionContent.classList.contains('hidden')) {
    sectionContent.classList.remove('hidden');
    toggleIcon.name = 'chevron-up-outline';
  } else {
    sectionContent.classList.add('hidden');
    toggleIcon.name = 'chevron-down-outline';
  }
}

// Save message and file upload functionality
function saveMessage() {
  const message = tinymce.get('message').getContent();
  const fileUpload = document.getElementById('fileUpload').files[0];
  const formData = new FormData();
  
  formData.append('message', message);
  formData.append('file', fileUpload);

  fetch('/notificageral/NotificaGeral/public/save-notification.php', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      if (data.success) {
          console.log('Notification saved successfully');
      } else {
          console.error('Error: ' + data.message);
      }
  })
  .catch(error => {
      console.error('Error:', error);
  });
}

// Edit message
function editMessage() {
  document.querySelector('.uploaded-content').style.display = 'none';
  document.querySelector('.message-area textarea').classList.remove('hidden');
  document.querySelector('.upload-area').classList.remove('hidden');
  document.querySelector('.edit-btn').classList.remove('show');
}

// Function to open and close the popup modals
function openEmailSettingsPopup() {
  document.getElementById('emailSettingsPopup').style.display = 'flex';
}

function closeEmailSettingsPopup() {
  document.getElementById('emailSettingsPopup').style.display = 'none';
}

function openSmsSettingsPopup() {
  document.getElementById('smsSettingsPopup').style.display = 'flex';
}

function closeSmsSettingsPopup() {
  document.getElementById('smsSettingsPopup').style.display = 'none';
}

function openPushSettingsPopup() {
  document.getElementById('pushSettingsPopup').style.display = 'flex';
}

function closePushSettingsPopup() {
  document.getElementById('pushSettingsPopup').style.display = 'none';
}

function openSettingsPopup() {
  document.getElementById('fullSettingsPopup').style.display = 'flex';
}

function closeFullSettingsPopup() {
  document.getElementById('fullSettingsPopup').style.display = 'none';
}

// Function to handle the progress bar and Generate All process
function generateAllNotifications() {
  const documents = [
    { name: 'Email Notification', id: 'email-notification' },
    { name: 'SMS Notification', id: 'sms-notification' },
    { name: 'Web Push Notification', id: 'web-push-notification' }
  ];

  const overlay = document.getElementById('overlay');
  const progressDialog = document.getElementById('progress-dialog');
  const progressBarFill = document.getElementById('progress-bar-fill');
  const progressBarText = document.getElementById('progress-bar-text');
  const generateAllButton = document.querySelector('.btn-gerar-todos');

  overlay.style.display = 'block';
  progressDialog.style.display = 'block';
  generateAllButton.disabled = true;
  generateAllButton.innerHTML = '<i class="fa fa-spinner fa-spin fa-2x"></i><b> Generating...</b>';
  progressBarText.innerHTML = '';

  documents.forEach((document, index) => {
    setTimeout(() => {
      const progress = ((index + 1) / documents.length) * 100;
      progressBarFill.style.width = `${progress}%`;

      progressBarText.innerHTML = `Generating "${document.name}"...`;
      const listItem = document.getElementById(document.id);
      const iconSpan = listItem.querySelector('.icon');
      iconSpan.className = 'icon fa fa-spinner fa-spin';

      if (index === documents.length - 1) {
        progressBarText.innerHTML = 'All notifications generated successfully!';
        generateAllButton.innerHTML = '<i class="fa fa-check fa-2x"></i><b> Completed</b>';
        iconSpan.className = 'icon fa fa-check';
        document.getElementById('progress-close').style.display = 'block';
      }
    }, index * 2000);
  });
}

// Close the progress popup
function closeProgressDialog() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('progress-dialog').style.display = 'none';
}

// Progress function for individual notifications
function generateNotification(type) {
  const overlay = document.getElementById('overlay');
  const progressDialog = document.getElementById('progress-dialog');
  const progressBarFill = document.getElementById('progress-bar-fill');
  const progressBarText = document.getElementById('progress-bar-text');

  overlay.style.display = 'block';
  progressDialog.style.display = 'block';
  progressBarFill.style.width = '0%';
  progressBarText.innerHTML = `Sending ${type}...`;

  setTimeout(() => {
    progressBarFill.style.width = '100%';
    progressBarText.innerHTML = `${type} sent successfully!`;
    document.getElementById('progress-close').style.display = 'block';
  }, 3000); // Simulating a delay
}

// Close the individual progress popup
function closeProgressPopup() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('progress-dialog').style.display = 'none';
}

