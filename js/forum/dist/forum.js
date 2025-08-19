
import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';
import Button from 'flarum/common/components/Button';
import RequestErrorModal from 'flarum/forum/components/RequestErrorModal';
import Stream from 'flarum/common/utils/Stream';

// Minimal forum UI for submitting funding request
app.initializers.add('funding-wallet', () => {
  console.log('Funding Wallet Forum JS loaded');
});
