<?php

namespace System\Console;

class Command
{
    use TraitCommand;

    // text
    public const TEXT_DIM           = 2;
    public const TEXT_RED           = 31;
    public const TEXT_GREEN         = 32;
    public const TEXT_YELLOW        = 33;
    public const TEXT_BLUE          = 34;
    public const TEXT_MAGENTA       = 35;
    public const TEXT_CYAN          = 36;
    public const TEXT_LIGHT_GRAY    = 37;
    public const TEXT_DEFAULT       = 39;
    public const TEXT_DARK_GRAY     = 90;
    public const TEXT_LIGHT_RED     = 91;
    public const TEXT_LIGHT_GREEN   = 92;
    public const TEXT_LIGHT_YELLOW  = 93;
    public const TEXT_LIGHT_BLUE    = 94;
    public const TEXT_LIGHT_MAGENTA = 95;
    public const TEXT_LIGHT_CYAN    = 96;
    public const TEXT_WHITE         = 97;
    // background color
    public const BG_RED           = 41;
    public const BG_GREEN         = 42;
    public const BG_YELLOW        = 43;
    public const BG_BLUE          = 44;
    public const BG_MAGENTA       = 45;
    public const BG_CYAN          = 46;
    public const BG_LIGHT_GRAY    = 47;
    public const BG_DEFAULT       = 49;
    public const BG_DARK_GRAY     = 100;
    public const BG_LIGHT_RED     = 101;
    public const BG_LIGHT_GREEN   = 102;
    public const BG_LIGHT_YELLOW  = 103;
    public const BG_LIGHT_BLUE    = 104;
    public const BG_LIGHT_MAGENTA = 105;
    public const BG_LIGHT_CYAN    = 106;
    public const BG_WHITE         = 107;
    // other
    public const BOLD            = 1;
    public const UNDERLINE       = 4;
    public const BLINK           = 5;
    public const REVERSE         = 7;
    public const HIDDEN          = 8;
    // reset
    public const RESET           = 0;
    public const RESET_BOLD      = 21;
    public const RESET_UNDERLINE = 24;
    public const RESET_BLINK     = 25;
    public const RESET_REVERSE   = 27;
    public const RESET_HIDDEN    = 28;
    // more code see https://misc.flogisoft.com/bash/tip_colors_and_formatting

    /**
     * Commandline input.
     *
     * @var string|array<int, string>
     */
    protected $CMD;

    /**
     * Commandline input.
     *
     * @var array<int, string>
     */
    protected $OPTION;

    /**
     * Base dir.
     *
     * @var string
     */
    protected $BASE_DIR;

    /**
     * Option object mapper.
     *
     * @var array<string, string|bool|int|null>
     */
    protected $option_mapper;

    /**
     * Parse commandline.
     *
     * @param array<int, string>                  $argv
     * @param array<string, string|bool|int|null> $default_option
     *
     * @return void
     */
    public function __construct(array $argv, $default_option = [])
    {
        // catch input argument from command line
        array_shift($argv); // remove index 0

        $this->CMD        = array_shift($argv) ?? '';
        $this->OPTION     = $argv;

        // parse the option
        $this->option_mapper = $default_option;
        foreach ($this->option_mapper($argv) as $key => $value) {
            $this->option_mapper[$key] = $value;
        }
    }

    /**
     * parse option to readable array option.
     *
     * @param array<int, string|bool|int|null> $argv Option to parse
     *
     * @return array<string, string|bool|int|null>
     */
    private function option_mapper(array $argv): array
    {
        $options         = [];
        $options['name'] = $argv[0] ?? '';

        foreach ($argv as $key => $option) {
            if ($this->isCommmadParam($option)) {
                $key_value = explode('=', $option);
                $name      = preg_replace('/-(.*?)/', '', $key_value[0]);

                // param have value
                if (isset($key_value[1])) {
                    $options[$name] = $key_value[1];
                    continue;
                }

                // search value in next param

                $next_key = $key + 1;
                $default  = true;

                $next           = $argv[$next_key] ?? $default;
                $options[$name] = $this->isCommmadParam($next) ? $default : $next;
            }
        }

        return $options;
    }

    private function isCommmadParam(string $command): bool
    {
        return substr($command, 0, 1) == '-' || substr($command, 0, 2) == '--';
    }

    /**
     * Get parse commandline parameters (name, value).
     *
     * @param string|null $default Default if parameter not found
     *
     * @return string|bool|int|null
     */
    protected function option(string $name, $default = null)
    {
        return $this->option_mapper[$name] ?? $default;
    }

    /**
     * Get parse commandline parameters (name, value).
     *
     * @param string $name
     *
     * @return string|bool|int|null
     */
    public function __get($name)
    {
        return $this->option($name);
    }

    /**
     * Default class to run some code.
     *
     * @return void
     */
    public function main()
    {
        echo $this->textGreen('Command') . "\n";
    }

    /**
     * @return string|array<string, array<int, string>> Text or array of text to be echo<
     */
    public function printHelp()
    {
        return [
            'option'   => [],
            'argument' => [],
        ];
    }
}
