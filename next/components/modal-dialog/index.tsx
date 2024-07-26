import React, { useState, useRef } from 'react';
import sendEmail from '../../lib/sendEmail';
import css from './modal-dialog.module.css';
import {FieldValues, SubmitHandler, useForm} from "react-hook-form";
import Image from "next/image";

export interface ModalDialogProps {
    closeModal(): void;
}

const ModalDialog: React.FC<ModalDialogProps> = ({ closeModal }) => {
    const { register, handleSubmit,
        formState: { errors, isValid } } = useForm();

    const form = useRef<HTMLFormElement>(null);
    const [isSuccessfullySubmitted, setIsSuccessfullySubmitted] =
        useState<boolean>(false);
    const [isLoading, setIsLoading] = useState<boolean>(false);

    const onFormSubmit:SubmitHandler<FieldValues> = async (data) => {
        setIsLoading(true);
            try {
                await fetch(`/api/demo`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ formData: data }),
                });

                await sendEmail(
                    data.name,
                    data.company,
                    data.email,
                    data.message,
                );

                await new Promise(resolve => setTimeout(resolve, 3000));
                setIsSuccessfullySubmitted(true);
            } catch (e) {
                console.log(e);
            }
            setIsLoading(false);
            setIsSuccessfullySubmitted(true);
        };

    return (
        <>
            <div className={`${css.root}`}>
                <div className={css.inner}>
                    <div onClick={closeModal} className={css.close}>
                        <Image
                            src="/close-modal.svg"
                            width={34}
                            height={34}
                            alt="close"
                        />
                    </div>
                    <div className={css.form}>
                        {!isSuccessfullySubmitted && (
                            <form ref={form} onSubmit={handleSubmit(onFormSubmit)}>
                                <p className={css.modalTitle}>Get in touch!</p>
                                <div className={css.inputBlock}>
                                    <input
                                        className={`${css.input}`}
                                        placeholder={'text'}
                                        {...register('name', {required: 'Name is required'})}
                                        type="text"
                                    />
                                    <div className={`${css.inputLabel}`}>
                                        Your name&nbsp;<span>*</span>
                                    </div>
                                    {errors.name && <p className={css.error}>{errors.name.message?.toString()}</p>}
                                </div>
                                <div className={css.inputBlock}>
                                    <input
                                        className={`${css.input}`}
                                        placeholder={'text'}
                                        {...register('company', {required: 'Company is required'})}
                                        type="text"
                                    />
                                    <div className={`${css.inputLabel}`}>
                                        Company&nbsp;<span>*</span>
                                    </div>
                                    {errors.company && <p className={css.error}>{errors.company.message?.toString()}</p>}
                                </div>
                                <div className={css.inputBlock}>
                                    <input
                                        className={`${css.input}`}
                                        placeholder={'text'}
                                        {...register('email', {
                                            required: 'Email is required',
                                            pattern: {
                                                value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i,
                                                message: "Invalid email address"
                                            }})}
                                    />
                                    <div className={`${css.inputLabel}`}>
                                        Email&nbsp;<span>*</span>
                                    </div>
                                    {errors.email && <p className={css.error}>{errors.email.message?.toString()}</p>}
                                </div>
                                <div className={css.inputBlock}>
                                    <input
                                        className={`${css.input} ${css.textArea}`}
                                        placeholder={'text'}
                                        {...register('message', {required: 'Message is required'})}
                                        type="text"
                                    />
                                    <div className={`${css.inputLabel} `}>
                                        Add a short message&nbsp;<span>*</span>
                                    </div>
                                    {errors.message && <p className={css.error}>{errors.message.message?.toString()}</p>}
                                </div>
                                <button
                                    type="submit"
                                    className={`${css.button} ${!isValid && css.disabled}`}
                                >
                                    {isLoading
                                    ? <Image layout="fill" src="/loading.gif" alt="loading"/>
                                    : 'Submit'}
                                </button>
                            </form>
                        )}
                        {isSuccessfullySubmitted && (
                            <div className={css.successMsg}>
                                <Image layout="fill" className={css.image} src="/chatting.svg" alt="chatting"/>
                                <div className={`${css.title}`}>Thank you.</div>
                                <div className={css.description}>
                                    We&apos;ll be in touch shortly!
                                </div>
                                <button
                                    onClick={() => {
                                        setIsSuccessfullySubmitted.bind(
                                        null,
                                        false,
                                    )
                                       closeModal();
                                    }}
                                    className={`${css.button}`}
                                >
                                    Close
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
            <div className={css.fader} />
        </>
    );
};

export default ModalDialog;
