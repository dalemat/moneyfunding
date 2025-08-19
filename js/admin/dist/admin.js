
import app from 'flarum/admin/app';

app.initializers.add('funding-wallet', () => {
  app.extensionData
    .for('funding-wallet')
    .registerSetting({
      setting: 'funding-wallet.deposit_address',
      label: 'Deposit Address',
      type: 'text',
    })
    .registerSetting({
      setting: 'funding-wallet.conversion_rate',
      label: 'Conversion Rate (1 token = X money)',
      type: 'number',
    })
    .registerSetting({
      setting: 'funding-wallet.token_decimals',
      label: 'Token Decimals',
      type: 'number',
    });
});
