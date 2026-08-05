<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipd_anaesthesia_department', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id');
            $table->uuid('ipd_anaesthesia_id');
            
            // PRE-ANAESTHESIA STATE (comma-separated: Awake, Apprehensive, Uncooperative, Calm, Asleep, Confused, Unresponsive, GCS)
            $table->text('pre_anaesthesia_state')->nullable();
            
            // VENTILATED PATIENT (comma-separated: VIA ETT, VIA Tracheostomy)
            $table->text('ventilated_patient')->nullable();
            
            // NPO Status
            $table->text('npo_status')->nullable();
            
            // PATIENT SAFETY (comma-separated: Airway Machine Checked, Pressure Points Checked, Eye Care, Ointment, Eye Pad)
            $table->text('patient_safety')->nullable();
            
            // GENERAL ANAESTHETIC TECHNIQUE - Pre-Oxygenation (comma-separated: Rapid Sequence, Cricoid Pressure)
            $table->text('pre_oxygenation')->nullable();
            
            // INDUCTION (comma-separated: Intravenous, Inhalational)
            $table->text('induction')->nullable();
            
            // LARYNGOSCOPY (comma-separated: Direct, Fibre Optic Scope, Blind, Others)
            $table->text('laryngoscopy')->nullable();
            
            // DIFFICULT INTUBATION (checkbox)
            $table->boolean('difficult_intubation')->default(false);
            
            // ENDO TRACHEAL TUBE (comma-separated: Oral, Nasal, Cuff, Regular, Reinforced, RAE, MLS Tube, Endoronchial, Laser, Size, Flexidart)
            $table->text('endotracheal_tube')->nullable();
            $table->string('endotracheal_tube_size')->nullable();
            $table->string('endotracheal_tube_fixed_at')->nullable();
            $table->string('endotracheal_tube_type')->nullable();
            $table->string('airway')->nullable();
            $table->string('airway_size')->nullable();
            
            // MASK ANAESTHESIA (comma-separated: Nasal Cannula, Oxygen Mask)
            $table->text('mask_anaesthesia')->nullable();
            
            // THROAT PACK (comma-separated: Inserted, Removed)
            $table->text('throat_pack')->nullable();
            
            // NASOGASTRIC TUBE (comma-separated: Inserted, Removed)
            $table->text('nasogastric_tube')->nullable();
            
            // MAINTENANCE (comma-separated: Inhalational, TWA, Regional)
            $table->text('maintenance')->nullable();
            
            // IV ACCESS (JSON or text for table with NO, SITE, SIZE, LOCATION)
            $table->longText('iv_access')->nullable();
            
            // REGIONAL ANAESTHESIA / ANALGESIA - CENTRAL BLOCKS SPINAL
            $table->text('central_blocks_spinal')->nullable();
            
            // CENTRAL BLOCKS EPIDURAL
            $table->text('central_blocks_epidural')->nullable();
            $table->string('central_blocks_epidural_g')->nullable();
            $table->string('central_blocks_spinal_needle_g')->nullable();
            
            // REGIONAL BLOCKS (comma-separated: Brachial Plexus, Sciatic, Femoral, Ankle, Caudal, Local)
            $table->text('regional_blocks')->nullable();
            
            // NERVE STIMULATOR (text for Yes, No, Effect, Complete, Incomplete)
            $table->text('nerve_stimulator')->nullable();
            
            // REGIONAL SUPPLEMENTS (comma-separated: GA, Sedation, Complication)
            $table->text('regional_supplements')->nullable();
            
            // DRUGS - REGIONAL ANESTHESIA (JSON or text for table with Drug Name, Conc., Vol.)
            $table->longText('drugs_regional')->nullable();
            
            // MONITORING (comma-separated: ECG, NBP, Pulse-Oximetry, EtCO2, ABP, CVP, Urine Output, Blood Loss, Other Fluids, Warmer)
            $table->text('monitoring')->nullable();
            
            // TEMPERATURE
            $table->text('temperature')->nullable();
            
            // TOTAL FLUIDS TRANSFUSED
            $table->integer('crystalloids_ml')->nullable();
            $table->integer('colloids_ml')->nullable();
            $table->integer('blood_ml')->nullable();
            
            // ANAESTHESIA TECHNIQUE BRIEF
            $table->text('anaesthesia_technique_brief')->nullable();
            
            // SUMMARY
            $table->text('summary')->nullable();
            
            // ABP DETAILS
            $table->text('abp_details')->nullable();
            
            // CVP DETAILS
            $table->text('cvp_details')->nullable();
            
            // UPLOAD PDF PATH
            $table->string('upload_pdf_path')->nullable();
            
            $table->timestamps();

            // Foreign key constraints (commented for flexibility)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('ipd_surgery_id')->references('id')->on('ipd_surgery')->onDelete('cascade');
            // $table->foreign('ipd_anaesthesia_id')->references('id')->on('ipd_anaesthesia')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_anaesthesia_department');
    }
};
