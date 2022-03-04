# Create your views here.
from django.http import HttpResponse
from django.template import Context, loader
from media.models import Disc

def graphs(request):
    discs = Disc.objects.all().order_by('label')
    t = loader.get_template('coda/index.html')
    c = Context({
        'discs': discs,
    })
    return HttpResponse(t.render(c))
