(function(){
  if (!window.app) return;
  function apiUrl(){return (app.forum&&app.forum.attribute)?app.forum.attribute('apiUrl'):'/api'}
  function ajax(method, path, data, cb){
    var xhr=new XMLHttpRequest();xhr.open(method, apiUrl()+path, true);
    xhr.setRequestHeader('Content-Type','application/json;charset=UTF-8');
    if (app.session && app.session.csrfToken) xhr.setRequestHeader('X-CSRF-Token', app.session.csrfToken);
    xhr.onload=function(){if(xhr.status>=200&&xhr.status<300){cb(null, JSON.parse(xhr.responseText||'{}'))}else{cb(new Error('HTTP '+xhr.status+': '+xhr.responseText))}};
    xhr.onerror=function(){cb(new Error('Network error'))};
    xhr.send(data?JSON.stringify(data):null);
  }
  function createRow(item,isAdmin){
    var wrap=document.createElement('div');wrap.className='FundingWallet-row';
    var left=document.createElement('div');left.style.flex='1';
    left.innerHTML='<div><strong>'+item.txHash+'</strong></div><div>'+item.amount+' — <em>'+item.status+'</em></div>';
    wrap.appendChild(left);
    if (item.reason){var r=document.createElement('div');r.textContent=item.reason;wrap.appendChild(r)}
    var actions=document.createElement('div');actions.className='FundingWallet-actions';
    if (isAdmin && item.status==='pending'){
      var approve=document.createElement('button');approve.textContent='Approve';
      approve.onclick=function(){ajax('POST','/funding-requests/'+item.id+'/approve',null,function(err){if(err){alert(err.message)}else{loadList()}})};
      var reject=document.createElement('button');reject.textContent='Reject';
      reject.onclick=function(){var reason=prompt('Reason (optional)')||'';ajax('POST','/funding-requests/'+item.id+'/reject',{reason:reason},function(err){if(err){alert(err.message)}else{loadList()}})};
      actions.appendChild(approve);actions.appendChild(reject);
    }
    wrap.appendChild(actions);
    return wrap;
  }
  var modal, listEl, isAdmin=false;
  function openModal(){
    if (modal) {modal.style.display='flex'; loadList(); return;}
    modal=document.createElement('div');modal.className='FundingWallet-modal';
    var card=document.createElement('div');card.className='FundingWallet-card';
    var header=document.createElement('div');header.style.display='flex';header.style.justifyContent='space-between';header.style.alignItems='center';
    var title=document.createElement('h3');title.textContent='Funding Requests';header.appendChild(title);
    var close=document.createElement('button');close.textContent='✕';close.onclick=function(){modal.style.display='none'};header.appendChild(close);
    card.appendChild(header);
    var actions=document.createElement('div');var btn=document.createElement('button');btn.textContent='Request Funding';
    btn.onclick=function(){var tx=prompt('Transaction Hash (0x...)'); if(!tx) return; var amount=prompt('Amount (tokens)'); if(!amount) return;
      ajax('POST','/funding-requests',{tx_hash:tx,amount:amount},function(err){if(err){alert('Error: '+err.message)}else{alert('Submitted');loadList()}})};
    actions.appendChild(btn);card.appendChild(actions);
    listEl=document.createElement('div');listEl.className='FundingWallet-list';card.appendChild(listEl);
    modal.appendChild(card);document.body.appendChild(modal);
    loadList();
  }
  function loadList(){
    listEl.innerHTML='Loading...';
    ajax('GET','/funding-requests',null,function(err,res){
      if(err){listEl.textContent='Failed to load';return;}
      var data=res.data||[];
      listEl.innerHTML='';
      isAdmin = !!(app.session && app.session.user && app.session.user.attribute && app.session.user.attribute('isAdmin'));
      data.forEach(function(d){
        var item={
          id: d.attributes && d.attributes.id || d.id,
          txHash: d.attributes && d.attributes.txHash || d.txHash,
          amount: d.attributes && d.attributes.amount || d.amount,
          status: d.attributes && d.attributes.status || d.status,
          reason: d.attributes && d.attributes.reason || d.reason
        };
        listEl.appendChild(createRow(item,isAdmin));
      });
    });
  }
  function addHeaderButton(){
    if (document.getElementById('FundingWalletHeaderBtn')) return;
    var container=document.querySelector('.Header-secondary')||document.querySelector('.Header-controls')||document.body;
    var a=document.createElement('a');a.id='FundingWalletHeaderBtn';a.href='#';a.textContent='Funding';a.style.marginLeft='8px';
    a.onclick=function(e){e.preventDefault();openModal();};
    container.insertBefore(a, container.firstChild);
  }
  app.initializers.add('funding-wallet-forum', function(){
    try{
      addHeaderButton();
      setTimeout(addHeaderButton, 1500);
      document.addEventListener('turbolinks:load', addHeaderButton);
    }catch(e){console.error('[funding-wallet] forum init failed', e);}
  });
})();