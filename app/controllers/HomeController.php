<?php

/**
* \HomeController
*/
class HomeController extends BaseController
{
    public function home()
    {
        $this->view = View::make('home')
                            ->with('article', Article::first())
                            ->withTitle('MFFC :-D')
                            ->withFuckMe('OK!');
        $this->mail = Mail::to(['info@zzs.me'])
                            ->from('MFFC test <hahahaha@163.com>')
                            ->title('Fuck Me')
                            ->content('<h1>Hello~~~ conme boby</h1>');
    }
}
