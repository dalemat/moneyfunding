import app from 'flarum/forum/app';
import Modal from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import m from 'mithril';

class FundingModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);
    this.tx = '';
    this.amount = '';
  }

  className() { return 'FundingModal Modal--small'; }
  title() { return 'Submit Funding Request'; }

  content() {
    return m('div', {className:'Modal-body'}, [
      m('div.Form-group', [
        m('label', 'Transaction Hash'),
        m('input.FormControl',{oninput: e => this.tx = e.target.value, value: this.tx})
      ]),
      m('div.Form-group', [
        m('label', 'Token Amount'),
        m('input.FormControl',{oninput: e => this.amount = e.target.value, value: this.amount})
      ]),
      m(Button, {className:'Button Button--primary', onclick: ()=> this.submit()}, 'Submit')
    ]);
  }

  submit() {
    app.request({
      method: 'POST',
      url: app.forum.attribute('apiUrl') + '/funding-requests',
      body: { tx_hash: this.tx, amount: this.amount }
    }).then(()=> { app.alerts.show({type:'success'}, 'Funding request submitted.'); this.hide(); })
      .catch(()=> { app.alerts.show({type:'error'}, 'Error submitting funding request.'); });
  }
}

app.initializers.add('funding-wallet-forum', () => {
  app.headerItems.add('funding-requests', m('a', {href:'#', onclick: e=> { e.preventDefault(); app.modal.show(new FundingModal()); } }, 'Request Funding'));
});
