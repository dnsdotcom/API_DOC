import httplib, urllib, json

class Provision(object):
	def __init__(self, auth_token, sandbox=False):
		self.auth_token = str(auth_token).upper()
		self.sandbox  = sandbox

	def query(self, command, args={}):
		if self.sandbox:
			conn = httplib.HTTPConnection('sandbox.dns.com')
		else:
			conn = httplib.HTTPSConnection('www.dns.com')

		# attach user authentication information if not already attached
		if 'AUTH_TOKEN' not in args:
			args['AUTH_TOKEN' ] = self.auth_token

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
		auth_token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXX',
		sandbox  = False
		)
	result = dnsObj.query('getDomains')

	if not result['meta']['success']:
		raise Exception('Error: %s' % result['meta']['error'])

	import pprint
	print pprint.pprint(result)
	
