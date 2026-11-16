const API_BASE = '/api/v1';

async function api(method, path, body) {
  const token = sessionStorage.getItem('token');
  const res = await fetch(API_BASE + path, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token ? { Authorization: 'Bearer ' + token } : {}),
    },
    body: body ? JSON.stringify(body) : undefined,
  });
  const data = await res.json().catch(() => ({}));
  if (res.status === 401 && path !== '/session/login') {
    sessionStorage.removeItem('token');
    sessionStorage.removeItem('account');
    location.href = '/login';
  }
  if (!res.ok) throw {
    status: res.status,
    error: data.error || (data.code ? { code: data.code, message: data.message } : undefined),
  };
  return data;
}