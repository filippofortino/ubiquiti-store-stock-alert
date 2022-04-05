/*
 * Since the code returned from Ubiquiti pages calls localStorage,
 * we need to mock it because it's not available in Lambda.
 */
class LocalStorageMock {
  getItem(key) { return null }
}

global.localStorage = new LocalStorageMock()

exports.handle = async function(event) {
  const data = Function('"use strict";return (' + event.code + ')')()

  return data;
}
