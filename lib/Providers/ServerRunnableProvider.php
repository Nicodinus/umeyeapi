<?php


namespace Nicodinus\UmeyeApi\Providers;


use Amp\Loop;
use Nicodinus\UmeyeApi\App;
use Nicodinus\UmeyeApi\TcpServer\Network\ServerHandler as TcpServerHandler;
use Nicodinus\UmeyeApi\UdpServer\Network\ServerHandler as UdpServerHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Amp\Promise\all;

class ServerRunnableProvider extends Command implements AppProviderInterface
{
    /**
     * @var string
     */
    protected static $defaultName = 'server_runnable:run';

    /**
     * @var App
     */
    protected ?App $app;

    /**
     * @param App $app
     * @return static
     */
    public function load(App $app): AppProviderInterface
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @return void
     */
    public function unload(): void
    {
        $this->app = null;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $self = &$this;

        Loop::run(function () use (&$self) {

            $udpServer = new UdpServerHandler("udp://0.0.0.0:8300");
            $tcpServer = new TcpServerHandler("tcp://0.0.0.0:8350");

            $promise1 = $udpServer->run();
            $promise2 = $tcpServer->run();

            $delay = 60000;
            $watcher = null;

            if ($delay > 0) {
                Loop::delay($delay, function () use (&$udpServer, &$tcpServer, &$watcher) {

                    $promise1 = $udpServer->gracefulShutdown();
                    $promise2 = $tcpServer->gracefulShutdown();

                    dump("shutdown pending");

                    yield all([
                        $promise1,
                        $promise2,
                    ]);

                    $watcher = null;

                    dump("shutdown handled");

                });

                dump("Warning: shutdown delayed in {$delay} ms.");
            }

            yield all([
                $promise1,
                $promise2,
            ]);

            if ($watcher) {
                Loop::cancel($watcher);
                $watcher = null;
            }

        });

        return 0;
    }
}