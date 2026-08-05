<?php
namespace App\Services;

use App\Contracts\CRUDContract;
use App\Models\SystemSettings;
use App\Models\Theme;
use App\Services\ImageService;
use App\Traits\FieldValuesTrait;
use App\Traits\SystemSettingsTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSettingsService extends GetImageService implements CRUDContract
{
    use FieldValuesTrait;
    use SystemSettingsTrait;

    protected $imageService;
    protected $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Services\ImageService $imageService
     */
    public function __construct(CheckValidation $checkValidationService, ImageService $imageService)
    {
        $this->checkValidationService = $checkValidationService;
        parent::__construct($imageService);
    }

    /**
     * @deprecated This method is not used.
     */
    public function create(Request $request): void
    {

    }

    /**
     * Summary of createSystemSettings
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function createSystemSettings(Request $request): string
    {
        $userId = Auth::user()->id;
        $this->checkValidationService->checkValidation($this->validate($request));
        $themeData = [
            'theme'                => $request->theme,
            'user_id'              => $userId,
            'primary_color'        => $request->primary_color,
            'tertiary_color'       => $request->tertiary_color,
            'secondary_color'      => $request->secondary_color,
            'bg_primary_color'     => $request->bg_primary_color,
            'bg_tertiary_color'    => $request->bg_tertiary_color,
            'text_primary_color'   => $request->text_primary_color,
            'bg_secondary_color'   => $request->bg_secondary_color,
            'text_tertiary_color'  => $request->text_tertiary_color,
            'text_secondary_color' => $request->text_secondary_color,
        ];

        if ($request->id !== "NA") {
            $systemSettings = SystemSettings::where('id', $request->id)->first();
            $theme          = Theme::where("user_id", $userId)->first();
            if (! empty($systemSettings) && ! empty($theme)) {
                $systemSettings->update($this->fileds($request));
                $themeData['system_settings_id'] = $systemSettings->id;
                Theme::where('user_id', $userId)->update($themeData);
                return (string) $systemSettings->id;
            } else {
                $systemSettings->update($this->fileds($request));
                $themeData['system_settings_id'] = $systemSettings->id;
                Theme::create($themeData);
                return (string) $systemSettings->id;
            }
        }
        $systemSettings                  = SystemSettings::create(array_merge($request->all(), ['user_id' => $userId]));
        $themeData['system_settings_id'] = $systemSettings->id;
        Theme::create($themeData);
        return (string) $systemSettings->id;
    }

    /**
     * @deprecated This method is not used.
     */
    public function update(Request $request, string | null $id): void
    {

    }

    /**
     * @deprecated This method is not used.
     */
    public function updateSystemSettings(Request $request, string | null $id): void
    {
        // $validate = $this->validate($request, true);
        // $this->checkValidationService->checkValidation($validate);
        // $systemSettings = SystemSettings::first();
        // if (!$systemSettings) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        // $systemSettings->update($this->fileds($request));
        // return (string) $systemSettings->id;
    }

    /**
     * @deprecated This method is not used.
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //write code here.
    }

    /**
     * @deprecated This method is not used.
     */
    public function delete(string $id): void
    {
        // write code here
    }

    /**
     * @deprecated This method is not used.
     */
    public function get(string $id): mixed
    {
        return null;
    }

    public function getSystemSettings()
    {
        return $this->all()[0];
        // $userId = Auth::user()->id;
        // return SystemSettings::where('user_id', $userId)->first();
    }

    /**
     * Summary of all
     * @param mixed $request
     * @throws \Exception
     * @return \Illuminate\Database\Eloquent\Collection<int, SystemSettings>
     */
    public function all(?Request $request = null): mixed
    {
        $user = Auth::user();
        if (! empty($user->system_settings_id)) {
            $settings = SystemSettings::where('id', $user->system_settings_id)->get();
        } else {
            $settings = SystemSettings::get();
        }
        $theme                               = Theme::where('system_settings_id', $settings[0]->id)->where('user_id', $user->id)->first();
        $settings[0]['theme']                = $theme?->theme;
        $settings[0]['user_id']              = $theme?->user_id;
        $settings[0]['profile_image']        = $user?->image;
        $settings[0]['primary_color']        = $theme?->primary_color ?? '#006D77';
        $settings[0]['tertiary_color']       = $theme?->tertiary_color ?? '#f7c078';
        $settings[0]['secondary_color']      = $theme?->secondary_color ?? '#E2952A';
        $settings[0]['bg_primary_color']     = $theme?->bg_primary_color ?? '#ebfdff';
        $settings[0]['bg_tertiary_color']    = $theme?->bg_tertiary_color ?? '#fff8f0';
        $settings[0]['text_primary_color']   = $theme?->text_primary_color;
        $settings[0]['bg_secondary_color']   = $theme?->bg_secondary_color ?? '#fff9f0';
        $settings[0]['text_tertiary_color']  = $theme?->text_tertiary_color;
        $settings[0]['text_secondary_color'] = $theme?->text_secondary_color;
        return $settings;
    }
}
