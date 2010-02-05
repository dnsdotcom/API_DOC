import httplib, urllib, json

class Provision(object):
	def __init__(self, email, password, sandbox=False):
		self.email    = email
		self.password = password
		self.sandbox  = sandbox

	def query(self, command, args={}):
		if self.sandbox:
			conn = httplib.HTTPConnection('sandbox.dns.com')
		else:
			conn = httplib.HTTPSConnection('www.dns.com')

		# attach user authentication information if not already attached
		if 'email' not in args:
			args['email' ] = self.email
		if 'password' not in args:
			args['password'] = self.password

		# create query string
		query_string = '/api/%s/?%s' % (command, urllib.urlencode(args))
		conn.request('GET', query_string)

		# read response
		resp = conn.getresponse()
		json_string = resp.read()
		# print json_string
		return json.loads(json_string)
			

if __name__ == "__main__":
	dnsObj = Provision(
		email    = 'xxxx@xxx.xxx',
		password = 'xxxxxxxxxxxx',
		sandbox  = False
		)
	result = dnsObj.query('getDomains')

	if not result['meta']['success']:
		raise Exception('Error: %s' % result['meta']['error'])

	import pprint
	print pprint.pprint(result)
	
