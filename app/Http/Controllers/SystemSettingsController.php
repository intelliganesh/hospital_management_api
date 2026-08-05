<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use OpenApi\Annotations as OA;
use App\Services\SystemSettingsService;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @OA\Tag(
 *     name="System settings",
 *     description="API endpoints for managing hospital system settings"
 * )
 */
class SystemSettingsController extends Controller
{
    use ResponseTrait;

    protected $systemSettingsService;

    /**
     * Summary of __construct
     * @param \App\Services\SystemSettingsService $systemSettingsService
     */
    public function __construct(SystemSettingsService $systemSettingsService)
    {
        $this->systemSettingsService = $systemSettingsService;
    }

    /**
     * @OA\Get(
     *     path="/api/get_system_settings",
     *     summary="Get all system settings",
     *     description="Retrieve all hospital system settings",
     *     tags={"System settings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="System settings successfully retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="System settings retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="settings",
     *                     type="object",
     *                     @OA\Property(property="hospital_logo", type="string", format="binary", description="Hospital logo image file"),
     *                     @OA\Property(property="hospital_name", type="string", example="Riverside Hospital"),
     *                     @OA\Property(property="hospital_prefix", type="string", example="HOS"),
     *                     @OA\Property(property="opd_prefix", type="string", example="OPD"),
     *                     @OA\Property(property="ipd_prefix", type="string", example="IPD"),
     *                     @OA\Property(property="patient_prefix", type="string", example="PAT"),
     *                     @OA\Property(property="appointment_prefix", type="string", example="APT"),
     *                     @OA\Property(property="payment_prefix", type="string", example="PAY"),
     *                     @OA\Property(property="test_prefix", type="string", example="TST"),
     *                     @OA\Property(property="primary_color", type="string", example="#4A90E2"),
     *                     @OA\Property(property="text_primary_color", type="string", example="#FFFFFF"),
     *                     @OA\Property(property="bg_primary_color", type="string", example="#E3F2FD"),
     *                     @OA\Property(property="secondary_color", type="string", example="#F5A623"),
     *                     @OA\Property(property="text_secondary_color", type="string", example="#FFFFFF"),
     *                     @OA\Property(property="bg_secondary_color", type="string", example="#FFF8E1"),
     *                     @OA\Property(property="tertiary_color", type="string", example="#7ED321"),
     *                     @OA\Property(property="text_tertiary_color", type="string", example="#FFFFFF"),
     *                     @OA\Property(property="bg_tertiary_color", type="string", example="#E8F5E9"),
    *                     @OA\Property(property="currency_symbol", type="string", example="$"),
    *                     @OA\Property(property="currency", type="string", example="USD"),
    *                     @OA\Property(property="upi", type="string", example="hospital@upi", description="UPI ID for payments"),
    *                     @OA\Property(property="qr_code", type="string", example="base64stringorurl.png", description="QR code image or URL for payments"),
    *                     @OA\Property(property="theme", type="string", example="light", enum={"dark", "light", "system"})
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         ref="#/components/responses/UnauthorizedResponse"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         ref="#/components/responses/ServerErrorResponse"
     *     )
     * )
     */
    public function all(?Request $request)
    {
        try {
            $settings = $this->systemSettingsService->all($request);
            return $this->successResponse(compact('settings'));
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('System settings data not found.');
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/add_or_edit_system_settings",
     *      summary="Create or update system settings",
     *      tags={"System settings"},
     *      description="Create new or update existing system settings",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "hospital_name", "hospital_prefix", "opd_prefix", "ipd_prefix", "patient_prefix",
     *                      "primary_color", "text_primary_color", "bg_primary_color",
     *                      "secondary_color", "text_secondary_color", "bg_secondary_color",
     *                      "tertiary_color", "text_tertiary_color", "bg_tertiary_color",
     *                      "currency_symbol", "currency", "theme"
     *                  },
     *                  @OA\Property(property="hospital_logo", type="string", format="binary", description="Hospital logo image file"),
     *                  @OA\Property(property="hospital_name", type="string", example="Riverside Hospital"),
     *                  @OA\Property(property="hospital_prefix", type="string", example="HOS"),
     *                  @OA\Property(property="opd_prefix", type="string", example="OPD"),
     *                  @OA\Property(property="ipd_prefix", type="string", example="IPD"),
     *                  @OA\Property(property="patient_prefix", type="string", example="PAT"),
     *                  @OA\Property(property="appointment_prefix", type="string", example="APT"),
     *                  @OA\Property(property="payment_prefix", type="string", example="PAY"),
     *                  @OA\Property(property="test_prefix", type="string", example="TST"),
     *                  @OA\Property(property="primary_color", type="string", example="#4A90E2"),
     *                  @OA\Property(property="text_primary_color", type="string", example="#FFFFFF"),
     *                  @OA\Property(property="bg_primary_color", type="string", example="#E3F2FD"),
     *                  @OA\Property(property="secondary_color", type="string", example="#F5A623"),
     *                  @OA\Property(property="text_secondary_color", type="string", example="#FFFFFF"),
     *                  @OA\Property(property="bg_secondary_color", type="string", example="#FFF8E1"),
     *                  @OA\Property(property="tertiary_color", type="string", example="#7ED321"),
     *                  @OA\Property(property="text_tertiary_color", type="string", example="#FFFFFF"),
     *                  @OA\Property(property="bg_tertiary_color", type="string", example="#E8F5E9"),
     *                  @OA\Property(property="currency_symbol", type="string", example="$"),
     *                  @OA\Property(property="currency", type="string", example="USD"),
     *                  @OA\Property(property="theme", type="string", example="light", enum={"dark", "light", "system"})
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="System settings created or updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="success"),
     *              @OA\Property(property="message", type="string", example="System settings created or updated successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1)
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="string", example="error"),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  example={"field_name": {"The field is required."}},
     *                  @OA\AdditionalProperties(
     *                      type="array",
     *                      @OA\Items(type="string")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          ref="#/components/responses/UnauthorizedResponse"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          ref="#/components/responses/NotFound"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          ref="#/components/responses/ServerErrorResponse"
     *      )
     * )
     */

    public function addOrEdit(Request $request)
    {
        try {
            return $this->successResponse(['id' => $this->systemSettingsService->createSystemSettings($request)]);
        } catch (NotFoundHttpException $notFound) {
            return $this->notFoundResponse($notFound);
        } catch (ValidationException $ve) {
            return $this->validationResponse($ve);
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
}