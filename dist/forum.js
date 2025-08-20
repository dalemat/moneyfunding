(function () {
  if (!window.app) return;
  function submitFunding(tx, amount) {
    var apiUrl = (app.forum && app.forum.attribute) ? app.forum.attribute('apiUrl') : '/api';
    var url = apiUrl + '/funding-requests';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
    xhr.onload = function () {
      if (xhr.status >= 200 && xhr.status < 300) {
        alert('Funding request submitted.');
      } else {
        alert('Error submitting funding request.');
      }
    };
    xhr.onerror = function () { alert('Network error.'); };
    xhr.send(JSON.stringify({ tx_hash: tx, amount: amount }));
  }
  function openPrompt() {
    var tx = prompt('Enter transaction hash (0x...)');
    if (!tx) return;
    var amount = prompt('Enter token amount (e.g. 1.5)');
    if (!amount) return;
    submitFunding(tx, amount);
  }
  function addButton() {
    if (document.getElementById('funding-wallet-btn')) return;
    var btn = document.createElement('a');
    btn.id = 'funding-wallet-btn';
    btn.href = '#';
    btn.textContent = 'Request Funding';
    btn.style.margin = '8px';
    btn.onclick = function (e) { e.preventDefault(); openPrompt(); };
    var header = document.querySelector('.Header-secondary') || document.querySelector('.Header-controls') || document.body;
    header.insertBefore(btn, header.firstChild);
  }
  app.initializers.add('funding-wallet-forum', function () {
    try {
      addButton();
      setTimeout(addButton, 1500);
      document.addEventListener('turbolinks:load', addButton);
    } catch (e) {
      console.error('[funding-wallet] forum init failed', e);
    }
  });
})();